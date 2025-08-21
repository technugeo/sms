<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;

use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\ForcePasswordReset;

//Route::get('/login', Login::class)->name('filament.admin.auth.login');

// Route::get('/force-password-reset', ForcePasswordReset::class)->name('filament.pages.auth.force-password-reset');



// Test route for sending email
Route::get('/mailtrap-test', function () {
    Mail::raw('Mailtrap test body', function ($m) {
        $m->to('aishah@nugeosolutions.com')->subject('Mailtrap test route');
    });

    return 'Sent (check Mailtrap inbox)';
});
