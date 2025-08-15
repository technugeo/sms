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
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#&*_';
        $password = '';
        $maxIndex = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $maxIndex)];
        }

        return $password;
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
            'role'         => 'S',
            'password'     => $hashedTempPassword,
        ]);

        // Generate matricId
        $course = Course::where('prog_code', $data['current_course'])->first();
        $progCode = $course ? str_pad($course->prog_code, 2, '0', STR_PAD_LEFT) : '00';
        $runningNo = str_pad($user->id, 4, '0', STR_PAD_LEFT);
        $intake = substr($data['intake_year'], 2, 2) . $this->convertMonthNameToNumber($data['intake_month']);
        $matricId = 'FIM12' . $progCode . $runningNo . $intake;

        // Prepare student data
        $data['user_id']    = $user->id;
        $data['matric_id']  = $matricId;
        $data['email']      = $studentEmail; // set the original form email for student
        $data['created_by'] = auth()->user()->email ?? 'system';
        $data['updated_by'] = auth()->user()->email ?? 'system';

        $student = Student::create($data);

        // Save temp password in a separate table
        \DB::table('password_reset_tokens')->insert([
            'user_id'            => $user->id,
            'email'              => $user->email,
            'token'              => Str::uuid(),
            'temp_hash_password' => $hashedTempPassword,
            'password'           => $tempPassword,
            'is_active'          => 'yes',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        return $student;
    }



}
