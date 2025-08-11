<?php
namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Http\Responses\Auth\LoginResponse as DefaultLoginResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    public ?array $data = null;

    public function mount(): void
    {
        parent::mount();
        $this->data = [
            'email' => null,
            'password' => null,
        ];
    }


    public function authenticate(): ?DefaultLoginResponse
    {
        \Log::info('Authenticate method called.');

        try {
            $credentials = $this->validate([
                'data.email' => ['required', 'email'],
                'data.password' => ['required'],
            ]);
            \Log::info('Validation passed.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ' . json_encode($e->errors()));
            throw $e; // re-throw after logging
        }

        if (! Auth::attempt([
            'email' => $this->data['email'],
            'password' => $this->data['password'],
        ])) {
            throw ValidationException::withMessages([
                'data.email' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();

        $allowedRoles = ['SA', 'AA', 'NAO', 'AO', 'S'];
        if (! in_array($user->role, $allowedRoles)) {
            Auth::logout();
            throw ValidationException::withMessages([
                'data.email' => 'Unauthorized role',
            ]);
        }

        return app(DefaultLoginResponse::class);
    }
}
