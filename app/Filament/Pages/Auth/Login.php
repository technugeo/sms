<?php
namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Filament\Http\Responses\Auth\LoginResponse as DefaultLoginResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Filament\Forms;

class Login extends BaseLogin
{
    public ?string $user_id = null;
    public ?string $password = null;

    protected function getForms(): array
    {
        return [
            'form' => $this->makeForm()
                ->schema([
                    Forms\Components\TextInput::make('user_id')
                        ->label('User ID')
                        ->required()
                        ->autocomplete(false)
                        ->autofocus(),

                    Forms\Components\TextInput::make('password')
                        ->label(__('Password'))
                        ->password()
                        ->required(),
                ])
                ->statePath(null), // Top-level properties, not nested in "data"
        ];
    }

    public function authenticate(): ?DefaultLoginResponse
    {
        $credentials = $this->validate([
            'user_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([
            'email' => $this->user_id, // Change to your DB column name
            'password' => $this->password,
        ])) {
            throw ValidationException::withMessages([
                'user_id' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();
        $allowedRoles = ['SA', 'AA', 'NAO', 'AO', 'S'];
        if (! in_array($user->role, $allowedRoles)) {
            Auth::logout();
            throw ValidationException::withMessages([
                'user_id' => 'Unauthorized role',
            ]);
        }

        return app(DefaultLoginResponse::class);
    }
}
