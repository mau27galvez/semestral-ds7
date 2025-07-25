<?php

use App\Livewire\CategoryManagement;
use App\Livewire\NewsManagement;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\UserManagement;
use App\Livewire\PublicHomepage;
use App\Livewire\NewsCategory;
use App\Livewire\NewsDetail;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', PublicHomepage::class)->name('home');
Route::get('/news/{news}', NewsDetail::class)->name('news.show');
Route::get('/categories/{category}', NewsCategory::class)->name('categories.show');

// auth
Route::redirect('settings', 'settings/profile');

Route::get('settings/profile', Profile::class)->name('settings.profile');
Route::get('settings/password', Password::class)->name('settings.password');
Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

// Dashboard routes (protected)
Route::middleware(['auth', 'verified', 'dashboard'])->prefix('dashboard')->name('dashboard.')->group(function () {

    Route::get('/', function () {
        return view('dashboard');
    })->name('index');

    // User Management
    Route::get('users', UserManagement::class)->name('users.index');

    // Category Management
    Route::get('categories', CategoryManagement::class)->name('categories.index');

    // News Management
    Route::get('news', NewsManagement::class)->name('news.index');
});

require __DIR__ . '/auth.php';
