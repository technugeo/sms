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
    public ?string $user_id = null;
    public ?string $password = null;
    public ?string $token = null;

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema([
                    Forms\Components\TextInput::make('user_id')
                        ->label('User ID')->required()->autocomplete(false)->autofocus(),
                    Forms\Components\TextInput::make('password')
                        ->label(__('Password'))->password()->required(),
                    Forms\Components\Hidden::make('token')
                        ->default(request()->query('token'))
                        ->dehydrated(false),
                ])->statePath(null),
        ];
    }



    public function mount(): void
    {
        $this->token = request()->query('token');
    }

    public function authenticate(): ?LoginResponse
    {
        $credentials = $this->validate([
            'user_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt([
            'email' => $this->user_id,
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'user_id' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();
        $allowedRoles = ['SA', 'AA', 'NAO', 'AO', 'S'];

        if (!in_array($user->role, $allowedRoles)) {
            Auth::logout();
            throw ValidationException::withMessages([
                'user_id' => 'Unauthorized role',
            ]);
        }

        \Log::info('Login attempt', [
            'user_id' => $this->user_id,
            'has_token' => $this->token !== null,
            'token_value' => $this->token,
            'user_email_verified' => $user->email_verified,
            'user_is_active' => $user->is_active,
        ]);

        if ($this->token) {
            $hasActiveToken = DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->where('is_active', 'yes')
                ->exists();

            if (is_null($user->email_verified) && $user->is_active == 0 && $hasActiveToken) {
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
