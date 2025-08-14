<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\Student;
use App\Models\User;
use App\Models\Course;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()_-+=';
        $password = '';
        $maxIndex = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $maxIndex)];
        }

        return $password;
    }

    protected function sendRegistrationEmail(User $user, string $tempPassword): void
    {
        // Insert password reset token record
        $token = Str::uuid();
        \DB::table('password_reset')->insert([
            'user_id'            => $user->id,
            'email'              => $user->email,
            'token'              => $token,
            'temp_hash_password' => Hash::make($tempPassword),
            'temp_password'      => $tempPassword,
            'is_active'          => 'yes',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $link = url('/login?token=' . $token);

        Mail::raw("
        Thank you for registering with us.

        Below are your login credentials:

        User ID: {$user->email}
        Temporary Password: {$tempPassword}
        Link: {$link}

        Thank you,
        SMS Support Team
        ", function ($message) use ($user) {
            $message->to('aishah@nugeosolutions.com') 
                    ->subject('Your SMS Account Credentials');
        });
    }

    protected function handleRecordCreation(array $data): Student
    {
        // Generate temp password
        $tempPassword = $this->generateTempPassword();

        // Create user with temporary email (will be matricId later)
        $user = User::create([
            'name'         => $data['full_name'],
            'email'        => $data['email'], 
            'profile_type' => Student::class,
            'role'         => 'S',
            'password'     => Hash::make($tempPassword),
        ]);

        // Generate matricId
        $course = Course::where('prog_code', $data['current_course'])->first();
        $progCode = $course ? str_pad($course->prog_code, 2, '0', STR_PAD_LEFT) : '00';
        $runningNo = str_pad($user->id, 4, '0', STR_PAD_LEFT);
        $intake = substr($data['intake_year'], 2, 2) . $this->convertMonthNameToNumber($data['intake_month']);
        $matricId = 'FIM12' . $progCode . $runningNo . $intake;

        // Update user's email to matricId
        $user->email = $matricId;
        $user->save();

        // Prepare student data
        unset($data['email']);
        $data['user_id'] = $user->id;
        $data['matric_id'] = $matricId;

        $student = Student::create($data);

        // Send registration email only if academic_status is 'Registered'
        if (isset($data['academic_status']) && $data['academic_status'] === 'Registered') {
            $this->sendRegistrationEmail($user, $tempPassword);
        }

        return $student;
    }
}
