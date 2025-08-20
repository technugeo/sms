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
        $tempPassword = $this->generateTempPassword();
        $hashedTempPassword = Hash::make($tempPassword);

        // Step 1: Determine roles from form
        $roles = $data['user']['roles'] ?? ['student']; // fallback role
        $primaryRole = $roles[0]; // first role as the primary

        // Step 2: Create User
        $user = User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => $hashedTempPassword,
            'profile_type' => 'App\\Models\\Staff',
            'status'       => 'Pending Activation',
            'role'         => $primaryRole, // set the users.role column
        ]);

        // Step 3: Sync roles in model_has_roles
        $user->syncRoles($roles);

        // Step 4: Prepare Staff data
        $data['user_id']     = $user->id;
        $data['full_name']   = $data['name'];
        $data['email']       = $user->email;
        $data['nationality'] = $data['nationality'] ?? 'Malaysia';
        unset($data['name'], $data['user']); // remove "user" array so Staff::create won't fail

        // Step 5: Create Staff
        $staff = Staff::create($data);

        // Step 6: Update User with profile_id
        $user->profile_id = $staff->id;
        $user->save();

        // Step 7: Insert password reset token
        $token = Str::uuid();
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

        $link = url('/login?token=' . $token);

        // Step 8: Send email
        Mail::html("
        <p>Hello <strong>{$user->name}</strong>,</p>

        <p>Thank you for registering with <strong>Food Institute of Malaysia</strong>.<br>
        Your staff account has been successfully created.</p>

        <p><strong>Please find your login details below:</strong><br>
        <strong>Staff Name:</strong> {$user->name}<br>
        <strong>User ID (Email):</strong> {$user->email}<br>
        <strong>Temporary Password:</strong> {$tempPassword}<br>
        
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

        \DB::table('audit_log')->insert([
            'action_by' => auth()->user()->email ?? 'system',
            'action_type' => 'create',
            'module' => 'staff',
            'record_id' => $staff->id,
            'notes' => 'Staff ' . $staff->full_name . ' created.',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'date_time' => now(),
        ]);

        return $staff;
    }
}
