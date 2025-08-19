<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;

class UpdatePassword extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationLabel = null; // hide from sidebar
    protected static bool $shouldRegisterNavigation = false; // don’t show in nav
    protected static string $view = 'filament.pages.update-password';

    public ?string $password = null;
    public ?string $password_confirmation = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('password')
                ->password()
                ->required()
                ->label('New Password'),

            Forms\Components\TextInput::make('password_confirmation')
                ->password()
                ->required()
                ->same('password')
                ->label('Confirm Password'),
        ];
    }

    public function save(): void
    {
        $user = auth()->user();

        $tempPassword = $this->password;
        $hashedTempPassword = Hash::make($tempPassword);

        // Capture original data for audit
        $originalData = [
            'password' => $user->getOriginal('password'),
        ];

        // Update password
        $user->update([
            'password' => $hashedTempPassword,
        ]);

        // Deactivate old tokens
        \DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->update(['is_active' => 'no']);

        // Insert new password token
        $token = \Str::random(64);
        \DB::table('password_reset_tokens')->insert([
            'user_id'            => $user->id,
            'email'              => $user->email,
            'token'              => $token,
            'temp_hash_password' => $hashedTempPassword,
            'password'           => $tempPassword,
            'is_active'          => 'yes',
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        // Audit log
        \DB::table('audit_log')->insert([
            'action_by'  => $user->email,
            'action_type'=> 'update',
            'module'     => 'user_password',
            'record_id'  => $user->id,
            'old_data'   => json_encode($originalData),
            'new_data'   => json_encode(['password' => $hashedTempPassword]),
            'notes'      => 'User' . $user->name . ' updated their password.',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'date_time'  => now(),
        ]);

        // Set temporary cookie, logout, and redirect
        cookie()->queue('post_password_update_notice', true, 1); // 1 minute
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        $this->redirect(route('filament.admin.auth.login'));
    }
}
