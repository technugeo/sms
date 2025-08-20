<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use Illuminate\Auth\Events\Logout;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (config('app.env') !== 'local') {
            URL::forceScheme('https');
        }
        // Listen for logout events
        \Event::listen(Logout::class, function ($event) {
            $user = $event->user;

            if ($user) {
                DB::table('audit_log')->insert([
                    'action_by'  => $user->email,
                    'action_type'=> 'logout',
                    'module'     => 'user',
                    'record_id'  => $user->id,
                    'old_data'   => json_encode(['logged_out_at' => now()]),
                    'new_data'   => json_encode([]),
                    'notes'      => 'User ' . $user->name . ' logged out',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                    'date_time'  => now(),
                ]);
            }
        });
    }
}
