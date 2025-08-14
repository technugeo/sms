<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use App\Models\Staff;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;


class CreateStaff extends CreateRecord
{
    protected static string $resource = StaffResource::class;

    /**
     * Generate a random temporary password.
     */
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

    protected function handleRecordCreation(array $data): Staff
    {
        // Generate temp password
        $tempPassword = $this->generateTempPassword();

        // Hash temp password
        $hashedTempPassword = Hash::make($tempPassword);

        // Create the user first (email = NRIC)
        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['nric'], 
            'role'      => $data['access_level'],
            'password'  => $hashedTempPassword,
            'user_type' => 'Employee',
        ]);

        // Generate reset token
        $token = Str::uuid();

        // Insert password reset token record
        \DB::table('password_reset')->insert([
            'user_id'            => $user->id,
            'email'              => $user->email,
            'token'              => $token,
            'temp_hash_password' => $hashedTempPassword,
            'temp_password'      => $tempPassword,
            'is_active'          => 'yes', 
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        
        $link = url('/login?token=' . $token);

        
        // Mail::raw("
        // Thank you for registering with us.

        // Below are your login credentials:

        // User ID: {$user->email}
        // Temporary Password: {$tempPassword}
        // Access Role: {$user->role}
        // Link: {$link}

        // Thank you,
        // SMS Support Team
        // ", function ($message) use ($data) {
        //     $message->to('aishah@nugeosolutions.com') 
        //             ->subject('Your SMS Account Credentials');
        // });

        $data['user_id']   = $user->id;
        $data['full_name'] = $data['name'];
        $data['email']     = $user->email;

        unset($data['name']); 

        // Create staff
        return Staff::create($data);
    }

}
