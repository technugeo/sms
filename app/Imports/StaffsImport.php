<?php

namespace App\Imports;

use App\Models\Staff;
use App\Models\User;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class StaffsImport implements ToCollection, WithHeadingRow
{
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

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            if (empty($row['full_name']) && empty($row['nric'])) {
                continue; 
            }

            $tempPassword = $this->generateTempPassword();
            $hashedTempPassword = Hash::make($tempPassword);

            $user = User::create([
                'name'      => $row['full_name'],
                'email'     => $row['nric'],
                'role'      => $row['access_level'] ?? 'staff',
                'password'  => $hashedTempPassword,
                'user_type' => 'Employee',
            ]);

            $token = Str::uuid();

            DB::table('password_reset')->insert([
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

            // For testing, send all emails to your own address
            $testingEmail = 'your_email@example.com'; // replace with your email

            Mail::raw("
            Thank you for registering with us.

            Below are your login credentials:

            User ID: {$user->email}
            Temporary Password: {$tempPassword}
            Access Role: {$user->role}
            Link: {$link}

            Thank you,
            SMS Support Team
            ", function ($message) use ($testingEmail) {
                $message->to('aishah@nugeosolutions.com') 
                        ->subject('Your SMS Account Credentials (TEST)');
            });

            Staff::create([
                'user_id'          => $user->id,
                'institute_id'     => $row['mqa_institute_id'] ?? null,
                'department_id'    => $row['department_id'] ?? null,
                'full_name'        => $row['full_name'],
                'nric'             => $row['nric'],
                'email'            => $row['email'] ?? $user->email,
                'phone_number'     => $row['phone_number'] ?? null,
                'passport_no'      => $row['passport_no'] ?? null,
                'gender'           => $row['gender'] ?? null,
                'marriage_status'  => $row['marriage_status'] ?? null,
                'race'             => $row['race'] ?? null,
                'religion'         => $row['religion'] ?? null,
                'citizen'          => $row['citizen'] ?? null,
                'nationality_type' => $row['nationality_type'] ?? null,
                'access_level'     => $row['access_level'] ?? null,
                'position'         => $row['position'] ?? null,
            ]);
        }
    }

}
