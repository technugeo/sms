<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\PasswordReset\ResetPassword;
use Filament\Http\Responses\Auth\Contracts\PasswordResetResponse;

use Filament\Forms;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class ForcePasswordReset extends ResetPassword
{
    public ?string $password = null;
    public ?string $password_confirmation = null;
    public ?string $token = null;
    public ?string $user_email = null;

    protected static string $routePath = 'force-password-reset';

    public function mount(?string $email = null, ?string $token = null): void
    {
        parent::mount($email, $token);

        // Use session and URL parameters to set user_email and token
        $sessionForceReset = Session::get('force_password_reset');
        $sessionEmail = Session::get('force_password_reset_email');
        $sessionToken = Session::get('force_password_reset_token');

        \Log::info('ForcePasswordReset mount called', [
            'email_param' => $email,
            'token_param' => $token,
            'session_force_reset' => $sessionForceReset,
            'session_email' => $sessionEmail,
            'session_token' => $sessionToken,
        ]);

        if (!$sessionForceReset) {
            $this->redirect('/login');
            return;
        }

        // Determine the email to use
        if ($email) {
            $this->user_email = $email;
        } elseif ($sessionEmail) {
            $this->user_email = $sessionEmail;
        }

        // Determine the token to use
        if ($token) {
            $this->token = $token;
        } elseif ($sessionToken) {
            $this->token = $sessionToken;
        }

        // Extra validation: redirect if missing email or token
        if (!$this->user_email || !$this->token) {
            \Log::warning('Missing email or token on mount', [
                'user_email' => $this->user_email,
                'token' => $this->token,
            ]);
            $this->redirect('/login');
            return;
        }
    }

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema([
                    Forms\Components\TextInput::make('user_email')
                        ->label('User Email')
                        ->disabled(),

                    Forms\Components\TextInput::make('password')
                        ->label('New Password')
                        ->password()
                        ->required(),

                    Forms\Components\TextInput::make('password_confirmation')
                        ->label('Confirm Password')
                        ->password()
                        ->required(),
                ]),
        ];
    }

    protected function getFormModel(): ?string
    {
        return null;
    }

    protected function getFormStatePath(): ?string
    {
        return null;
    }

    public function getFormState(): array
    {
        return [
            'user_email' => $this->user_email,
        ];
    }

    public function submit(): ?PasswordResetResponse
    {
        \Log::info('submit() called in ForcePasswordReset', [
            'token' => $this->token,
            'user_email' => $this->user_email,
        ]);

        $state = $this->form->getState();

        $validator = Validator::make($state, [
            'password' => ['required', 'string', 'confirmed', 'min:8'],
        ]);

        if ($validator->fails()) {
            throw ValidationException::withMessages($validator->errors()->toArray());
        }

        $user = User::where('email', $this->user_email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'user_email' => 'User not found.',
            ]);
        }

        if (!$this->token) {
            throw ValidationException::withMessages([
                'token' => 'Reset token is missing or invalid.',
            ]);
        }

        // Manual token validation against your password_reset_tokens table
        $hasActiveToken = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->where('token', $this->token)
            ->where('is_active', 'yes')
            ->exists();

        if (!$hasActiveToken) {
            \Log::warning('Invalid token during submit', [
                'email' => $user->email,
                'token' => $this->token,
            ]);
            throw ValidationException::withMessages([
                'token' => 'This password reset token is invalid.',
            ]);
        }

        // Update user password and status (make sure hash_password is your real password column)
        $user->update([
            'hash_password' => Hash::make($state['password']),
            'email_verified' => now(),
            'is_active' => 1,
        ]);

        // Mark token as inactive
        DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->where('token', $this->token)
            ->update(['is_active' => 'no']);

        // Clear session keys related to force reset
        Session::forget([
            'force_password_reset',
            'force_password_reset_email',
            'force_password_reset_user_id',
            'force_password_reset_token',
        ]);

        // Redirect to login page after successful reset
        return redirect()->route('filament.admin.auth.login')->with('success', 'Password reset successfully.');
    }
}
