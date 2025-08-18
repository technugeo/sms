<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

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
            \Log::info("Updating student email from {$student->user->email} to {$data['email']}");
            $student->user->update(['email' => $data['email']]);
        }
        

        unset($data['email']); // prevent saving email to student table
        $data['updated_by'] = auth()->user()->email ?? 'system';

        return $data;
    }

    protected function sendRegistrationEmail(Student $student, string $tempPassword): void
    {
        $user = $student->user;

        $token = Str::uuid();
        \DB::table('password_reset_tokens')->insert([
            'user_id'            => $user->id,
            'email'              => $user->email,
            'token'              => $token,
            'temp_hash_password' => Hash::make($tempPassword),
            'password'           => $tempPassword,
            'is_active'          => 'yes',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $link = url('/login?token=' . $token);

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
        //     $message->to($user->email)
        //             ->subject('Your SMS Account Credentials');
        // });

        // \Log::info("Registration email triggered for {$user->email}");


    }

    protected function afterSave(): void
    {
        $student = $this->record->fresh();
        $newStatus = $student->academic_status instanceof AcademicEnum
            ? $student->academic_status->value
            : $student->academic_status;

        // Log the update to audit_log
        \DB::table('audit_log')->insert([
            'action_by'  => auth()->user()->email ?? 'system',
            'action_type'=> 'update',
            'module'     => 'student',
            'record_id'  => $student->id,
            'old_data'   => json_encode($this->record->getOriginal()), // before save
            'new_data'   => json_encode($student->getChanges()),       // after save
            'notes'      => 'Student ' . $student->full_name . ' updated.',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'date_time'  => now(),
        ]);


        // Send email if the status is REGISTERED
        if ($newStatus === AcademicEnum::REGISTERED->value) {
            $this->sendRegistrationEmail($student, ''); // No password needed
        }
    }



}
