<?php

namespace App\Filament\Pages;

use App\Models\Staff;
use App\Filament\Resources\StaffResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class StaffProfile extends ViewRecord
{
    protected static string $resource = StaffResource::class;

    protected static ?string $slug = 'staff-profile';
    protected static ?string $navigationLabel = 'My Profile';
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    // Hide from sidebar navigation
    protected static bool $shouldRegisterNavigation = false;

    // public static function canAccess(array $parameters = []): bool
    // {
    //     return auth()->check() && auth()->user()->hasRole('S');
    // }

    public function mount(string|int $record = null): void
    {
        $user = Auth::user();

        $staff = Staff::where('email', $user->email)->firstOrFail();

        parent::mount($staff->getKey());
    }

    public function getRelations(): array
    {
        return StaffResource::getRelations();
    }
}
