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
        // Create the user record
        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make('password'),
            'user_type' => 'Employee',
        ]);

        // Assign the new user's ID to the staff data
        $data['user_id'] = $user->id;

        // Remove user-specific fields not present in the 'staff' table
        unset($data['name'], $data['email']);

        // Create and return the staff record
        return Staff::create($data);
    }
}
