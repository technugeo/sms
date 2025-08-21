<?php

namespace App\Filament\Resources\StaffResource\Pages;

use App\Filament\Resources\StaffResource;
use App\Models\Staff;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

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
        // Step 0: Generate temporary password
        $tempPassword = $this->generateTempPassword();
        $hashedTempPassword = Hash::make($tempPassword);

        // Extract role IDs safely
        $roleIds = collect($data['user']['roles'] ?? [])
            ->map(fn($r) => is_array($r) ? $r['id'] : $r)
            ->filter()
            ->values()
            ->toArray();

        // Get role names from IDs
        $roles = Role::whereIn('id', $roleIds)->pluck('name')->toArray();

        // Primary role
        $primaryRole = $roles[0] ?? 'non_academic_officer';


        // Step 2: Create User
        $user = User::create([
            'name'         => $data['full_name'],
            'email'        => $data['email'],
            'password'     => $hashedTempPassword,
            'profile_type' => 'App\\Models\\Staff',
            'status'       => 'Pending Activation',
            'role'         => $primaryRole,
        ]);

        // Step 3: Sync roles
        $user->syncRoles($roles);

        // Step 4: Handle department/faculty logic
        $data['department_id'] = ($data['department_id'] ?? '') === 'N/A' ? null : $data['department_id'];
        $data['faculty_id'] = ($data['faculty_id'] ?? '') === 'N/A' ? null : $data['faculty_id'];

        if ($primaryRole === 'academic_officer') {
            $data['department_id'] = null;
        }

        // Step 5: Prepare Staff data
        $staffData = $data;
        $staffData['user_id'] = $user->id;
        $staffData['email'] = $user->email;
        $staffData['role'] = $primaryRole;
        $staffData['nationality'] = $data['nationality'] ?? 'Malaysia';

        // Assign institute_id
        if (auth()->user()->hasRole('super_admin')) {
            // Use selected mqa_institute_id directly
            $staffData['institute_id'] = $data['institute_id'];
        } else {
            // Auto inherit from logged-in staff
            $staffData['institute_id'] = optional(auth()->user()->staff)->institute_id;

            if (!$staffData['institute_id']) {
                throw new \Exception('Your account does not have an institute assigned. Please contact super admin.');
            }
        }




        unset($staffData['user']); // remove nested user data to avoid mass assignment issues

        
        // Step 6: Create Staff
        $staff = Staff::create($staffData);

        // Step 7: Update user profile_id
        $user->profile_id = $staff->id;
        $user->save();

        // Step 8: Insert password reset token
        $token = Str::uuid();
        \DB::table('password_reset_tokens')->insert([
            'user_id' => $user->id,
            'email' => $user->email,
            'token' => $token,
            'temp_hash_password' => $hashedTempPassword,
            'password' => $tempPassword,
            'is_active' => 'yes',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $link = url('/login?token=' . $token);

        // Step 9: Send email
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

        // Step 10: Audit log
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
