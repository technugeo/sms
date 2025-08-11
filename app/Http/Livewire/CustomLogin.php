<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class CustomLogin extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    public function authenticate()
    {
        $credentials = $this->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $this->remember)) {
            $user = Auth::user();

            // Role check - allow only 'superadmin' or 'admin'
            if (!in_array($user->role, ['superadmin', 'admin'])) {
                Auth::logout();
                $this->addError('email', 'You do not have permission to login.');
                return;
            }

            session()->regenerate();

            return redirect()->intended(route('filament.pages.dashboard'));
        }

        $this->addError('email', trans('auth.failed'));
    }

    public function render()
    {
        return view('livewire.custom-login');
    }
}
