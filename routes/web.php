<?php
use App\Http\Controllers\SchoolSwitchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MagicController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


use App\Models\LoginToken;


Route::middleware(['auth'])->group(function () {
    Route::get('/switch-school/{school}', [SchoolSwitchController::class, 'switch'])
        ->name('school.switch');
});

Route::get('/magic-login/{token}', [MagicController::class, 'login'])
    ->name('magic.login');
