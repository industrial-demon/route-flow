<?php

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function (Request $request) {})->name('dashboard');
});

Route::livewire('root', 'features.auth.register');
Route::livewire('seller', 'seller::dashboard');

Route::prefix('seller')->name('seller.')->group(function () {
    Route::livewire('', 'seller::dashboard')->name('dashboard');
    Route::livewire('products', 'seller::products')->name('products');
    Route::livewire('profile', 'seller::dashboard')->name('profile');
    Route::livewire('connections', 'seller::dashboard')->name('connections');
    Route::livewire('management', 'seller::dashboard')->name('management');
    Route::livewire('statistics', 'seller::dashboard')->name('statistics');
});

require __DIR__ . '/settings.php';
