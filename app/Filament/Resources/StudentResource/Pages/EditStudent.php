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

    // Store original data for audit logging
    protected array $originalData = [];

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($record) {
                    \DB::table('audit_log')->insert([
                        'action_by'  => auth()->user()->email ?? 'system',
                        'action_type'=> 'delete',
                        'module'     => 'student', // or 'staff'
                        'record_id'  => $record->id,
                        'old_data'   => json_encode($record->toArray()),
                        'new_data'   => json_encode([]),
                        'notes'      => 'Soft-deleted record',
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'date_time'  => now(),
                    ]);
                }),
            Actions\ForceDeleteAction::make() // similarly attach before/after callback
                ->before(function ($record) {
                    \DB::table('audit_log')->insert([
                        'action_by'  => auth()->user()->email ?? 'system',
                        'action_type'=> 'force_delete',
                        'module'     => 'student', // or 'staff'
                        'record_id'  => $record->id,
                        'old_data'   => json_encode($record->toArray()),
                        'new_data'   => json_encode([]),
                        'notes'      => 'Permanently deleted record',
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'date_time'  => now(),
                    ]);
                }),
            Actions\RestoreAction::make() // optional, you can log restore too
                ->after(function ($record) {
                    \DB::table('audit_log')->insert([
                        'action_by'  => auth()->user()->email ?? 'system',
                        'action_type'=> 'restore',
                        'module'     => 'student',
                        'record_id'  => $record->id,
                        'old_data'   => json_encode([]),
                        'new_data'   => json_encode($record->toArray()),
                        'notes'      => 'Restored record',
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                        'date_time'  => now(),
                    ]);
                }),
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

        // Capture original academic status
        $this->originalStatus = $student->academic_status instanceof AcademicEnum
            ? $student->academic_status->value
            : $student->academic_status;

        // Capture original data for audit logging
        $this->originalData = $student->getOriginal();

        // Update email in related user table
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

        if (empty($tempPassword)) {
            $tempPassword = Str::random(8); // generate temp password if empty
        }

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

        Mail::html("
        <p>Hello <strong>{$user->name}</strong>,</p>

        <p>Thank you for registering with <strong>Food Institute of Malaysia</strong>.<br>
        Your student account has been successfully created.</p>

        <p><strong>Please find your login details below:</strong><br>
        <strong>Student Name:</strong> {$user->name}<br>
        <strong>User ID (Email):</strong> {$user->email}<br>
        <strong>Temporary Password:</strong> {$tempPassword}<br>
        
        <p style=\"text-align: center;\">
            <a href=\"{$link}\" 
            style=\"display: inline-block; padding: 10px 20px; font-size: 16px; color: #ffffff; background-color: #007bff; text-decoration: none; border-radius: 5px;\">
            Click here to Login
            </a>
        </p>

        <p><strong>Important:</strong><br>
        You will be required to update your password immediately after your first login.<br>
        Do not share your login credentials with anyone.</p>

        <p>Thank you,<br>
        NuSmart Support Team</p>
        ", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Student - Account Credentials');
        });

        \Log::info("Registration email triggered for {$user->email}");
    }

    protected function afterSave(): void
    {
        $student = $this->record; // already saved

        // Get new academic status
        $newStatus = $student->academic_status instanceof AcademicEnum
            ? $student->academic_status->value
            : $student->academic_status;

        // Log the update to audit_log
        \DB::table('audit_log')->insert([
            'action_by'  => auth()->user()->email ?? 'system',
            'action_type'=> 'update',
            'module'     => 'student',
            'record_id'  => $student->id,
            'old_data'   => json_encode($this->originalData),
            'new_data'   => json_encode($student->getChanges()), // changes made during this save
            'notes'      => 'Student ' . $student->full_name . ' updated.',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'date_time'  => now(),
        ]);

        // Send registration email if status changed to REGISTERED
        if ($newStatus === AcademicEnum::REGISTERED->value 
            && $this->originalStatus !== AcademicEnum::REGISTERED->value) {
            $this->sendRegistrationEmail($student, ''); // generate temp password inside method
        }
    }
}
