<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentsImport implements ToCollection, WithHeadingRow
{
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

    protected function sendRegistrationEmail(User $user, string $tempPassword): void
    {
        // Insert password reset token record
        $token = Str::uuid();
        DB::table('password_reset_tokens')->insert([
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

    protected function handleStudentCreation(array $row): Student
    {
        // Prepare temp password
        $tempPassword = $this->generateTempPassword();
        $hashedTempPassword = Hash::make($tempPassword);

        // Create user
        $user = User::create([
            'name'         => $row['full_name'],
            'email'        => $row['email'],
            'profile_type' => Student::class,
            'role'         => 'student', // Student role
            'password'     => $hashedTempPassword,
        ]);

        $user->assignRole('student');

        // Generate matricId
        $course = Course::where('prog_code', $row['course_code'])->first();
        $progCode = $course ? str_pad($course->prog_code, 2, '0', STR_PAD_LEFT) : '0';
        $runningNo = str_pad($user->id, 3, '0', STR_PAD_LEFT);
        $intake = substr($row['intake_year'], 2, 2) . $this->convertMonthNameToNumber($row['intake_month']);
        $matricId =  $progCode . $runningNo . $intake;

        // Create student
        $student = Student::create([
            'user_id'          => $user->id,
            'matric_id'        => $matricId,
            'current_course'   => $row['course_code'],
            'intake_month'     => $row['intake_month'],
            'intake_year'      => $row['intake_year'],
            'full_name'        => $row['full_name'],
            'nric'             => $row['nric'] ?? null,
            'email'            => $row['email'],
            'phone_number'     => $row['phone_number'],
            'nationality'      => $row['nationality'],
            'passport_no'      => $row['passport_no'] ?? null,
            'gender'           => $row['gender'],
            'marriage_status'  => $row['marriage_status'],
            'race'             => $row['race'],
            'religion'         => $row['religion'],
            'citizen'          => $row['citizen'],
            'nationality_type' => $row['nationality_type'],
            'academic_status'  => $row['academic_status'] ?? null,
            'created_by'       => auth()->user()->email ?? 'system',
            'updated_by'       => auth()->user()->email ?? 'system',
        ]);

        // Only send registration email if academic_status is "Registered"
        if (!empty($row['academic_status']) && strtolower($row['academic_status']) === 'registered') {
            $this->sendRegistrationEmail($user, $tempPassword);
        }

        return $student;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['full_name']) && empty($row['nric'])) {
                continue;
            }

            // Convert row collection to array
            $this->handleStudentCreation($row->toArray());
        }
    }
}
