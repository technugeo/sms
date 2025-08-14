<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Student;
use App\Enum\AcademicEnum;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    // Store the original academic status before saving
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

        // Save the original academic status as a string
        $this->originalStatus = $student->academic_status instanceof AcademicEnum
            ? $student->academic_status->value
            : $student->academic_status;

        // Update user email if provided
        if (isset($data['email']) && $student->user) {
            $student->user->update([
                'email' => $data['email'],
            ]);
        }

        unset($data['email']); // prevent saving email to students table

        return $data;
    }

    protected function sendRegistrationEmail(Student $student, string $tempPassword): void
    {
        $user = $student->user;

        // Insert password reset token record
        $token = Str::uuid();
        \DB::table('password_reset_tokens')->insert([
            'user_id'            => $user->id,
            'email'              => $user->email,
            'token'              => $token,
            'temp_hash_password' => Hash::make($tempPassword),
            'temp_password'      => $tempPassword,
            'is_active'          => 'yes',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // $link = url('/login?token=' . $token);

        // Mail::raw("
        // Thank you for registering with us.

        // Below are your login credentials:

        // Student name: {$user->name}
        // User ID: {$user->email}
        // Temporary Password: {$tempPassword}
        // Link: {$link}

        // Thank you,
        // SMS Support Team
        // ", function ($message) use ($user) {
        //     $message->to('aishah@nugeosolutions.com') 
        //             ->subject('Your SMS Account Credentials');
        // });
    }

    protected function afterSave(): void
    {
        $student = $this->record->fresh();
        $newStatus = $student->academic_status instanceof AcademicEnum
            ? $student->academic_status->value
            : $student->academic_status;

        // Send email only if original status was not 'Registered' and new status is 'Registered'
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
