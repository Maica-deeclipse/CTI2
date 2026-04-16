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

Route::post('/register', [DemoController::class, 'register']);
Route::post('/login', [DemoController::class, 'login']);

require __DIR__.'/settings.php';
