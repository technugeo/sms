<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Filament\Forms;

class Login extends BaseLogin
{
    public ?string $email = null;
    public ?string $password = null;
    public ?string $token = null;

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema([
                    Forms\Components\TextInput::make('email')
                        ->label('Email')
                        ->email()
                        ->required()
                        ->autocomplete(false)
                        ->autofocus(),
                    Forms\Components\TextInput::make('password')
                        ->label(__('Password'))
                        ->password()
                        ->required(),
                    Forms\Components\Hidden::make('token')
                        ->default(request()->query('token'))
                        ->dehydrated(false),
                ])->statePath(null),
        ];
    }

    public function mount(): void
    {
        $this->token = request()->query('token');

        parent::mount();

        if (request()->hasCookie('post_password_update_notice')) {
            \Filament\Notifications\Notification::make()
                ->title('Password updated successfully')
                ->body('Please login again with your new password.')
                ->success()
                ->send();
        }

    }


    public function authenticate(): ?LoginResponse
    {
        $credentials = $this->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt([
            'email' => $this->email,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();
        $allowedRoles = ['SA', 'AA', 'NAO', 'AO', 'S'];

        if (!in_array($user->role, $allowedRoles)) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Unauthorized role',
            ]);
        }

        
        if ($user->status === 'Suspended') {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Your account has been suspended. Please contact support.',
            ]);
        }

        
        if (is_null($user->email_verified_at) && $user->is_active == 0 && $user->status === 'Pending Activation') {
            $user->email_verified_at = now();
            $user->is_active = 1;
            $user->status = 'Activated';
            $user->save();

            $user->refresh();
        }



        \Log::info('Login attempt', [
            'email' => $this->email,
            'has_token' => $this->token !== null,
            'token_value' => $this->token,
            'user_email_verified_at' => $user->email_verified_at, 
            'user_is_active' => $user->is_active,
            'user_status' => $user->status,
        ]);


        if ($this->token) {
            $hasActiveToken = DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->where('is_active', 'yes')
                ->exists();

            if (is_null($user->email_verified_at) && $user->is_active == 0 && $hasActiveToken) {
                session([
                    'force_password_reset_tokens' => true,
                    'force_password_reset_tokens_email' => $user->email,
                    'force_password_reset_tokens_user_id' => $user->id,
                    'force_password_reset_tokens_token' => $this->token,
                ]);

                Auth::logout();

                $this->redirectRoute('filament.pages.auth.force-password-reset', [
                    'email' => $user->email,
                    'token' => $this->token,
                ]);

                return null;
            }

        }

        return app(LoginResponse::class);
    }

}
