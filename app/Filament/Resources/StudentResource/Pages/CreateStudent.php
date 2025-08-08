<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\Student;
use App\Models\User;
use App\Models\Address;
use App\Models\Course;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    // Helper function to convert month name to two-digit number string
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

    protected function handleRecordCreation(array $data): Student
    {
        
        $user = User::create([
            'name'         => $data['full_name'],
            'email'        => $data['email'],
            'profile_type' => Student::class,
            'password'     => Hash::make('password'),
        ]);

        
        unset($data['email']);
        $data['user_id'] = $user->id;

        
        $course = Course::where('prog_code', $data['current_course'])->first();

        $progCode = $course ? $course->prog_code : '00'; 

        
        $progCode = str_pad($progCode, 2, '0', STR_PAD_LEFT);

        
        $runningNo = str_pad($user->id, 4, '0', STR_PAD_LEFT);

        
        $intakeYear = $data['intake_year']; 
        $intakeMonth = $data['intake_month']; 

        $intake = substr($intakeYear, 2, 2) . $this->convertMonthNameToNumber($intakeMonth);

        $matricId = $progCode . $runningNo . $intake;

        $data['matric_id'] = $matricId;

        
        // if (strtolower($data['citizen']) === 'foreign') {
        //     $localAddress = Address::create([
        //         'user_id'   => $user->id,
        //         'address_1' => 'TEMP LOCAL ADDR',
        //         'address_2' => '-', // add this
        //     ]);

        //     $foreignAddress = Address::create([
        //         'user_id'   => $user->id,
        //         'address_1' => 'TEMP FOREIGN ADDR',
        //         'address_2' => '-', // add this
        //     ]);


        //     $data['local_address_id'] = $localAddress->id;
        //     $data['foreign_address_id'] = $foreignAddress->id;
        // }

        
        return Student::create($data);
    }
}
