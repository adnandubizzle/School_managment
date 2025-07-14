<?php
use App\Http\Controllers\SchoolSwitchController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MagicLoginController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


use App\Models\LoginToken;


Route::middleware(['auth'])->group(function () {
    Route::get('/switch-school/{school}', [SchoolSwitchController::class, 'switch'])
        ->name('school.switch');
});

Route::get('/magic-login/{token}', [MagicLoginController::class, 'login'])
    ->name('magic.login');
//Magic login route with debugging
Route::get('/magic-login/{token}', function ($token) {
    try {
        // Find the login token
        $loginToken = LoginToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$loginToken) {
            return redirect('/admin/login')->with('error', 'Invalid or expired login token.');
        }

        // Get the user
        $user = User::find($loginToken->user_id);
        
        if (!$user) {
            return redirect('/admin/login')->with('error', 'User not found.');
        }

        // Delete the used token first
        $loginToken->delete();

        // Log the user in using Filament's auth guard
        Auth::guard('web')->login($user);
        
        // Verify the user is logged in
        if (Auth::check()) {
            return redirect('/admin')->with('success', 'Successfully logged in as ' . $user->name);
        } else {
            return redirect('/admin/login')->with('error', 'Failed to authenticate user.');
        }
        
    } catch (\Exception $e) {
        return redirect('/admin/login')->with('error', 'An error occurred during login.');
    }
})->name('magic.login');