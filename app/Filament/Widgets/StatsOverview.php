<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Student;
use App\Models\Staff;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $todayUsers = User::whereDate('created_at', today())->count();
        $todayStudents = Student::whereDate('created_at', today())->count();
        $todayStaff = Staff::whereDate('created_at', today())->count();

        return [
            Stat::make('Total Users', User::count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->description("{$todayUsers} new added today")
                ->icon('heroicon-o-users')
                ->color('success'),

            Stat::make('Total Students', Student::count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->description("{$todayStudents} new added today")
                ->icon('heroicon-o-academic-cap')
                ->color('danger'),

            Stat::make('Total Staff', Staff::count())
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->description("{$todayStaff} new added today")
                ->icon('heroicon-o-briefcase')
                ->color('success'),
        ];
    }

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user && $user->hasAnyRole(['SA', 'AA']);
    }
}
