<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;

use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Filament\Navigation\NavigationItem;
use Filament\Navigation\UserMenuItem;
use App\Filament\Resources\StudentResource;

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

use BezhanSalleh\FilamentShield\FilamentShieldPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->brandName('Student Management System')

            // -------- USER MENU ITEMS --------
            ->userMenuItems([
                'profile' => UserMenuItem::make()
                    ->label(fn () => auth()->user()->name)
                    ->icon('heroicon-o-user-circle')
                    ->url(function (): string {
                        $student = \App\Models\Student::where('email', auth()->user()->email)->first();

                        return $student
                            ? StudentResource::getUrl('view', ['record' => $student->getKey()])
                            : '#';
                    }),

                'password' => UserMenuItem::make()
                    ->label('Update Password')
                    ->icon('heroicon-o-key')
                    ->url(fn (): string => \App\Filament\Pages\UpdatePassword::getUrl(panel: 'admin')),
            ])


            // -------- PAGES --------



            // -------- RESOURCE DISCOVERY --------
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')

            // -------- WIDGETS --------
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Widgets\StatsOverview::class,
            ])


            // -------- MIDDLEWARE --------
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([

            ]);
    }
}
