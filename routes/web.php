<?php

use App\Http\Controllers\DemoController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::inertia('/', 'welcome', [
    'canRegister' => Features::enabled(Features::registration()),
])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

// ✅ SECURE: CSRF is automatically enforced by Laravel's web middleware
// group (VerifyCsrfToken) for all POST routes in this file.
//
// ✅ SECURE: throttle:5,1 = max 5 attempts per minute per IP (rate limiting)
Route::middleware(['throttle:5,1'])->group(function () {
    Route::post('/register', [DemoController::class, 'register']);
    Route::post('/login', [DemoController::class, 'login']);
});

require __DIR__.'/settings.php';
