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
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789@#&*_';
        $password = '';
        $maxIndex = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, $maxIndex)];
        }

        return $password;
    }

    protected function handleRecordCreation(array $data): Staff
    {
        // Step 1: Generate temporary password
        $tempPassword = $this->generateTempPassword();
        $hashedTempPassword = Hash::make($tempPassword);

        // Step 2: Create User first (needed for Staff.user_id)
        $user = User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => $hashedTempPassword,
            'profile_type' => 'App\\Models\\Staff',
            'status'       => 'Pending Activation',
        ]);

        // Assign role if provided
        if (!empty($data['access_level'])) {
            $user->assignRole($data['access_level']);
        }

        // Step 3: Prepare Staff data
        $data['user_id']    = $user->id;
        $data['full_name']  = $data['name'];
        $data['email']      = $user->email;
        $data['nationality']= $data['nationality'] ?? 'Malaysia';
        unset($data['name']);

        // Step 4: Create Staff
        $staff = Staff::create($data);

        // Step 5: Update User with profile_id
        $user->profile_id = $staff->id;
        $user->save();

        // Step 6: Insert password reset token
        $token = \Illuminate\Support\Str::uuid();
        \DB::table('password_reset_tokens')->insert([
            'user_id'            => $user->id,
            'email'              => $user->email,
            'token'              => $token,
            'temp_hash_password' => $hashedTempPassword,
            'password'           => $tempPassword,
            'is_active'          => 'yes',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // Step 7: Return the Staff object
        return $staff;
    }


}
