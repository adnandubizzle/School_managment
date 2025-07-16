<?php
use App\Http\Controllers\SchoolSwitchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MagicController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


use App\Models\LoginToken;
use App\Filament\Resources\SchoolResource\Pages\BulkInviteUsers;
use App\Http\Controllers\BulkInviteController;


Route::middleware(['auth'])->group(function () {
    Route::get('/admin/schools/{school}/bulk-invite', [BulkInviteController::class, 'showForm'])->name('bulk-invite.form');
    Route::post('/admin/schools/{school}/bulk-invite', [BulkInviteController::class, 'handleUpload'])->name('bulk-invite.upload');
    Route::get('/switch-school/{school}', [SchoolSwitchController::class, 'switch'])
        ->name('school.switch');
});

Route::get('/magic-login/{token}', [MagicController::class, 'login'])
    ->name('magic.login');



