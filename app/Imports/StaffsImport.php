<?php

namespace App\Imports;

use App\Models\Staff;
use App\Models\User;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use Illuminate\Support\Facades\DB;

class StaffsImport implements ToCollection, WithHeadingRow
{
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

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Skip if full_name and nric are empty
            if (empty($row['full_name']) && empty($row['nric'])) {
                continue; 
            }

            // Skip if email is empty
            if (empty($row['email'])) {
                continue;
            }

            $tempPassword = $this->generateTempPassword();
            $hashedTempPassword = Hash::make($tempPassword);

            // Step 1: Create User
            $user = User::create([
                'name'         => $row['full_name'],
                'email'        => $row['email'],
                'password'     => $hashedTempPassword,
                'profile_type' => 'App\\Models\\Staff',
                'status'       => 'Pending Activation',
                'role'         => $row['access_level'],
            ]);

            // Step 2: Assign role using Spatie's assignRole method from access_level
            if (!empty($row['access_level'])) {
                $user->assignRole($row['access_level']);
            }

            // Step 3: Insert password reset token
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

            $link = url('/login?token=' . $token);

            Mail::html("
            <p>Hello <strong>{$user->name}</strong>,</p>

            <p>Thank you for registering with <strong>Food Institute of Malaysia</strong>.<br>
            Your staff account has been successfully created.</p>

            <p><strong>Please find your login details below:</strong></p>
            <table style='border-collapse: collapse;'>
                <tr>
                    <td style='padding: 5px;'><strong>Student Name:</strong></td>
                    <td style='padding: 5px;'>{$user->name}</td>
                </tr>
                <tr>
                    <td style='padding: 5px;'><strong>User ID (Email):</strong></td>
                    <td style='padding: 5px;'>{$user->email}</td>
                </tr>
                <tr>
                    <td style='padding: 5px;'><strong>Temporary Password:</strong></td>
                    <td style='padding: 5px;'>{$tempPassword}</td>
                </tr>
            </table>
        
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
                        ->subject('Employee - Account Credentials');
            });

            // Step 4: Create Staff record
            $staff = Staff::create([
                'user_id'           => $user->id,
                'institute_id'      => $row['mqa_institute_id'] ?? null,
                // 'department_id'     => $row['department_id'] ?? null,
                // 'faculty_id'        => $row['faculty_id'] ?? null,
                'full_name'         => $row['full_name'],
                'nric'              => $row['nric'],
                'email'             => $row['email'] ?? $user->email,
                'phone_number'      => $row['phone_number'] ?? null,
                'passport_no'       => $row['passport_no'] ?? null,
                'gender'            => $row['gender'] ?? null,
                'marriage_status'   => $row['marriage_status'] ?? null,
                'race'              => $row['race'] ?? null,
                'religion'          => $row['religion'] ?? null,
                'citizen'           => $row['citizen'] ?? null,
                'nationality'       => $row['nationality'] ?? 'Malaysia', 
                'nationality_type'  => $row['nationality_type'] ?? null,
                'role'              => $row['access_level'] ?? null,
                'position'          => $row['position'] ?? null,
                'staff_type'        => $row['staff_type'] ?? null,
                'employment_status' => $row['employment_status'] ?? null,
            ]);

            // Step 5: Update User's profile_id to link back to Staff
            $user->profile_id = $staff->id;
            $user->save();
        }
    }
}
