<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Placeholder routes for navigation - will be replaced by Livewire page components in later phases
Route::middleware('auth')->group(function () {
    Route::view('/dashboard', 'welcome')->name('dashboard.index');
    Route::view('/kanban', 'welcome')->name('kanban.index');
    Route::view('/team', 'welcome')->name('team.index');
    Route::view('/settings', 'welcome')->name('settings.index');

    Route::post('/logout', function () {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');
});

// Guest routes - will be replaced by Livewire page components
Route::middleware('guest')->group(function () {
    Route::view('/login', 'welcome')->name('login');
    Route::view('/register', 'welcome')->name('register');
});
