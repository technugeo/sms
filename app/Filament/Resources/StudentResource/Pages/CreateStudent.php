<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\Student;
use App\Models\User;
use App\Models\Address;
use App\Models\Course;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
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

    protected function handleRecordCreation(array $data): Student
    {
        // Generate temp password
        $tempPassword = $this->generateTempPassword();

        // Hash temp password
        $hashedTempPassword = Hash::make($tempPassword);

        // Create user first with original email temporarily
        $user = User::create([
            'name'         => $data['full_name'],
            'email'        => $data['email'], // temporary, will update after matricId generation
            'profile_type' => Student::class,
            'role'         => 'S',
            'password'     => $hashedTempPassword,
        ]);

        // Now generate matricId using $user->id
        $course = Course::where('prog_code', $data['current_course'])->first();
        $progCode = $course ? $course->prog_code : '00';
        $progCode = str_pad($progCode, 2, '0', STR_PAD_LEFT);

        $runningNo = str_pad($user->id, 4, '0', STR_PAD_LEFT);

        $intakeYear = $data['intake_year'];
        $intakeMonth = $data['intake_month'];
        $intake = substr($intakeYear, 2, 2) . $this->convertMonthNameToNumber($intakeMonth);

        $matricId = 'FIM12' . $progCode . $runningNo . $intake;

        // Update user email to matricId
        $user->email = $matricId;
        $user->save();

        // Insert password reset token record using matricId as email
        \DB::table('password_reset_tokens')->insert([
            'user_id'            => $user->id,
            'email'              => $matricId,
            'token'              => \Str::uuid(),
            'temp_hash_password' => $hashedTempPassword,
            'temp_password'      => $tempPassword,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // Prepare student data
        unset($data['email']);
        $data['user_id'] = $user->id;
        $data['matric_id'] = $matricId;

        return Student::create($data);
    }

}
