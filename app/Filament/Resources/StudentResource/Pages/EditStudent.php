<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Student;
use App\Enum\AcademicEnum;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected ?string $originalStatus = null;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $student = $this->record->loadMissing('user');
        $data['email'] = $student->user?->email ?? '';
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $student = $this->record;

        $this->originalStatus = $student->academic_status instanceof AcademicEnum
            ? $student->academic_status->value
            : $student->academic_status;

        if (isset($data['email']) && $student->user) {
            $student->user->update(['email' => $data['email']]);
        }

        unset($data['email']); // prevent saving email to student table
        $data['updated_by'] = auth()->user()->email ?? 'system';

        return $data;
    }

    protected function sendRegistrationEmail(Student $student, string $tempPassword): void
    {
        $user = $student->user;

        \DB::table('password_reset_tokens')->insert([
            'user_id'            => $user->id,
            'email'              => $user->email,
            'token'              => Str::uuid(),
            'temp_hash_password' => Hash::make($tempPassword),
            'password'           => $tempPassword,
            'is_active'          => 'yes',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);
    }

    protected function afterSave(): void
    {
        $student = $this->record->fresh();
        $newStatus = $student->academic_status instanceof AcademicEnum
            ? $student->academic_status->value
            : $student->academic_status;

        if (($this->originalStatus ?? null) !== AcademicEnum::REGISTERED->value
            && $newStatus === AcademicEnum::REGISTERED->value) {

            $tempPassword = Str::random(12);

            if ($student->user) {
                $student->user->password = Hash::make($tempPassword);
                $student->user->save();
            }

            $this->sendRegistrationEmail($student, $tempPassword);
        }
    }
}
