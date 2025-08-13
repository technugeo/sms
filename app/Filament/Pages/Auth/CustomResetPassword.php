<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\PasswordReset\ResetPassword;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CustomResetPassword extends ResetPassword
{
    public ?string $email = null;
    public ?string $token = null;
    public ?string $password = null;
    public ?string $password_confirmation = null;

    protected static string $routePath = 'reset-password';

    public function mount(?string $email = null, ?string $token = null): void
    {
        parent::mount($email, $token);

        $this->email = $email ?? request()->query('email');
        $this->token = $token ?? request()->query('token');
    }

    public function submit()
    {
        $data = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'token' => ['required'],
        ]);

        // Validate the token using Laravel's broker
        $status = Password::broker()->reset(
            $data,
            function ($user, $password) {
                $user->password = Hash::make($password);
                $user->email_verified_at = now();
                $user->is_active = 1; // your custom field
                $user->save();
            }
        );

        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('filament.admin.auth.login')->with('success', 'Password reset successfully.');
        }

        $this->addError('token', __('This password reset token is invalid.'));
    }
}
