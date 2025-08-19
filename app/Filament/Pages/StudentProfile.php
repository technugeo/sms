<?php

namespace App\Filament\Pages;

use App\Models\Student;
use App\Filament\Resources\StudentResource;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class StudentProfile extends ViewRecord
{
    protected static string $resource = StudentResource::class;

    protected static ?string $slug = 'student-profile';
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

        $student = Student::where('email', $user->email)->firstOrFail();

        parent::mount($student->getKey());
    }

    public function getRelations(): array
    {
        return StudentResource::getRelations();
    }
}
