<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\Student;
use App\Models\User;
use App\Models\Course;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function convertMonthNameToNumber(string $monthName): string
    {
        $months = [
            'January' => '01', 'February' => '02', 'March' => '03',
            'April' => '04', 'May' => '05', 'June' => '06',
            'July' => '07', 'August' => '08', 'September' => '09',
            'October' => '10', 'November' => '11', 'December' => '12',
        ];

        return $months[$monthName] ?? '00';
    }

    protected function generateTempPassword(int $length = 12): string
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#&*_';
        $password = '';
        $maxIndex = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $maxIndex)];
        }

        return $password;
    }

    protected function sendRegistrationEmail(User $user, string $tempPassword, string $token): void
    {
        $link = url('/login?token=' . $token);

        Mail::html("
        <p>Hello <strong>{$user->name}</strong>,</p>

        <p>Thank you for registering with <strong>Food Institute of Malaysia</strong>.<br>
        Your student account has been successfully created.</p>

        <p><strong>Please find your login details below:</strong><br>
        <strong>Student Name:</strong> {$user->name}<br>
        <strong>User ID (Email):</strong> {$user->email}<br>
        <strong>Temporary Password:</strong> {$tempPassword}</p>

        <p style=\"text-align: center;\">
            <a href=\"{$link}\" 
            style=\"
                    display: inline-block;
                    padding: 10px 20px;
                    font-size: 16px;
                    color: #ffffff;
                    background-color: #007bff;
                    text-decoration: none;
                    border-radius: 5px;
            \">
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
    }

    protected function handleRecordCreation(array $data): Student
    {
        $tempPassword = $this->generateTempPassword();
        $hashedTempPassword = Hash::make($tempPassword);

        // Store original email for student table
        $studentEmail = $data['email'];

        // Create the user
        $user = User::create([
            'name'         => $data['full_name'],
            'email'        => $data['email'],
            'profile_type' => Student::class,
            'role'         => 'student',
            'password'     => $hashedTempPassword,
        ]);

        $user->assignRole('student');

        // Generate matricId
        $course = Course::where('prog_code', $data['current_course'])->first();
        $progCode = $course ? str_pad($course->prog_code, 2, '0', STR_PAD_LEFT) : '0';
        $runningNo = str_pad($user->id, 3, '0', STR_PAD_LEFT);
        $intake = substr($data['intake_year'], 2, 2) . $this->convertMonthNameToNumber($data['intake_month']);
        $matricId =  $progCode . $runningNo . $intake;

        // Prepare student data
        $data['user_id']    = $user->id;
        $data['matric_id']  = $matricId;
        $data['email']      = $studentEmail;
        $data['created_by'] = auth()->user()->email ?? 'system';
        $data['updated_by'] = auth()->user()->email ?? 'system';

        $student = Student::create($data);

        \DB::table('audit_log')->insert([
            'action_by' => auth()->user()->email ?? 'system',
            'action_type' => 'create',
            'module' => 'student',
            'record_id' => $student->id,
            'notes' => 'Student ' . $student->full_name . ' created with matric ID ' . $student->matric_id,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'date_time' => now(),
        ]);

        // Always save temp password in password_reset_tokens
        $token = Str::uuid();
        \DB::table('password_reset_tokens')->insert([
            'user_id'            => $user->id,
            'email'              => $user->email,
            'token'              => $token,
            'temp_hash_password' => $hashedTempPassword,
            'password'           => $tempPassword,
            'is_active'          => 'yes',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // Send email only if status is Registered
        if (isset($data['academic_status']) && $data['academic_status'] === 'Registered') {
            $this->sendRegistrationEmail($user, $tempPassword, $token);
        }

        return $student;
    }
}
