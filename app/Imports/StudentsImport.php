<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Filament\Resources\StudentResource\Pages\CreateStudent;
use App\Enum\AcademicEnum;


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
            'role'         => 'S', // Student role
            'password'     => Hash::make($tempPassword),
        ]);

        $user->assignRole('S');

        $token = Str::uuid();
        DB::table('password_reset_tokens')->insert([
            'user_id'            => $user->id,
            'email'              => $user->email,
            'token'              => $token,
            'temp_hash_password' => $hashedTempPassword,
            'password'           => $tempPassword,
            'is_active'          => 'yes',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // Generate matricId
        $course = Course::where('prog_code', $row['course_code'])->first();
        $progCode = $course ? str_pad($course->prog_code, 2, '0', STR_PAD_LEFT) : '00';
        $runningNo = str_pad($user->id, 4, '0', STR_PAD_LEFT);
        $intake = substr($row['intake_year'], 2, 2) . $this->convertMonthNameToNumber($row['intake_month']);
        $matricId = 'FIM12' . $progCode . $runningNo . $intake;

        // Create student
        $student = Student::create([
            'user_id'          => $user->id,
            'matric_id'        => $matricId,
            'current_course'   => $row['course_code'],
            'intake_month'     => $row['intake_month'],
            'intake_year'      => $row['intake_year'],
            'full_name'        => $row['full_name'],
            'nric'             => $row['nric'],
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
