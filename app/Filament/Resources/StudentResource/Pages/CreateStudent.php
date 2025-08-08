<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use App\Models\Student;
use App\Models\User;
use App\Models\Address;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;

    protected function handleRecordCreation(array $data): Student
    {
        // Step 1: Create the User
        $user = User::create([
            'name'         => $data['full_name'],
            'email'        => $data['email'],
            'profile_type' => Student::class,
            'password'     => Hash::make('password'),
        ]);

        // Step 2: Prepare Student data
        unset($data['email']);
        $data['user_id'] = $user->id;

        // Step 3: Conditionally create addresses if 'citizen' is 'foreign'
        if ($data['citizen'] === 'foreign') {
            $localAddress = Address::create([
                'user_id'   => $user->id,
                'address_1' => 'TEMP LOCAL ADDR', // replace with actual fields if collected
            ]);

            $foreignAddress = Address::create([
                'user_id'   => $user->id,
                'address_1' => 'TEMP FOREIGN ADDR',
            ]);

            $data['local_address_id'] = $localAddress->id;
            $data['foreign_address_id'] = $foreignAddress->id;
        }

        // Step 4: Create and return Student
        return Student::create($data);
    }
}
