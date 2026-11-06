<?php

use App\Enums\UserRoles;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function (Request $request) {
    })->name('dashboard');
});

Route::livewire('root', 'features.auth.register');

require __DIR__ . '/settings.php';
