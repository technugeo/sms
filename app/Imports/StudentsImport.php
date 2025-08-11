<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use App\Models\Course;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
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

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['full_name']) && empty($row['nric'])) {
                continue;
            }

            // First create the user
            $user = User::create([
                'name' => $row['full_name'],
                'email' => $row['email'],
                'profile_type' => Student::class,
                'password' => Hash::make('password'), // or generate dynamically
            ]);

            // Prepare matric_id
            $course = Course::where('prog_code', $row['course_code'])->first();
            $progCode = $course ? $course->prog_code : '00';
            $progCode = str_pad($progCode, 2, '0', STR_PAD_LEFT);

            // Use user id as running number (matches form logic)
            $runningNo = str_pad($user->id, 4, '0', STR_PAD_LEFT);

            $intakeYear = $row['intake_year'];
            $intakeMonth = $row['intake_month'];
            $intake = substr($intakeYear, 2, 2) . $this->convertMonthNameToNumber($intakeMonth);

            $matricId = $progCode . $runningNo . $intake;

            // Now create the student with user_id attached
            Student::create([
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
                'academic_status'  => $row['academic_status'],
            ]);
        }
    }
}
