<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use App\Models\Staff;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;

class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;

    protected function handleRecordCreation(array $data): Staff
    {
        // Create the user first
        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['nric'], // or actual email field
            'role'      => $data['access_level'],
            'password'  => Hash::make('password'),
            'user_type' => 'Employee',
        ]);

        // Assign user_id to staff data
        $data['user_id'] = $user->id;

        // Map 'name' to 'full_name' for Staff
        $data['full_name'] = $data['name'];

        // Provide email for staff if needed
        $data['email'] = $user->email;

        // Clean up $data
        unset($data['name']); 

        // Create staff
        return Staff::create($data);
    }

}
