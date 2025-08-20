<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class CustomLoginController extends Controller
{
    public function showLoginForm(Request $request)
    {
        // Show password update notification only once
        if ($request->hasCookie('post_password_update_notice')) {
            \Filament\Notifications\Notification::make()
                ->title('Password updated successfully')
                ->body('Please login again with your new password.')
                ->success()
                ->send();

            Cookie::queue(Cookie::forget('post_password_update_notice'));
        }

        return view('filament.pages.auth.login', [
            'token' => $request->query('token'),
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        $user = Auth::user();

        // Allowed roles
        $allowedRoles = Role::pluck('name')->toArray();
        if (!$user->hasAnyRole($allowedRoles)) {
            Auth::logout();
            throw ValidationException::withMessages(['email' => 'Unauthorized role']);
        }

        // User status check
        if (in_array($user->status, ['Suspended', 'Deleted'])) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => $user->status === 'Suspended'
                    ? 'Your account has been suspended. Please contact support.'
                    : 'Your account has been deleted. Please contact support.',
            ]);
        }

        // Auto-activate pending users
        if (is_null($user->email_verified_at) && $user->is_active == 0 && $user->status === 'Pending Activation') {
            $user->email_verified_at = now();
            $user->is_active = 1;
            $user->status = 'Activated';
            $user->save();
        }

        // Logging
        \Log::info('Login attempt', [
            'email' => $request->email,
            'user_email_verified_at' => $user->email_verified_at,
            'user_is_active' => $user->is_active,
            'user_status' => $user->status,
        ]);

        DB::table('audit_log')->insert([
            'action_by' => $user->email,
            'action_type' => 'login',
            'module' => 'auth',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'date_time' => now(),
            'notes' => 'User logged in successfully',
        ]);

        // Token logic
        $token = $request->input('token');
        if ($token) {
            $hasActiveToken = DB::table('password_reset_tokens')
                ->where('email', $user->email)
                ->where('is_active', 'yes')
                ->exists();

            if (is_null($user->email_verified_at) && $user->is_active == 0 && $hasActiveToken) {
                $request->session()->put([
                    'force_password_reset_tokens' => true,
                    'force_password_reset_tokens_email' => $user->email,
                    'force_password_reset_tokens_user_id' => $user->id,
                    'force_password_reset_tokens_token' => $token,
                ]);

                Auth::logout();

                return redirect()->route('filament.pages.auth.force-password-reset', [
                    'email' => $user->email,
                    'token' => $token,
                ]);
            }
        }

        // Successful login redirect (Filament dashboard)
        return redirect()->intended(config('filament.path', '/admin'));
    }
}
