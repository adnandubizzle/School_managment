<?php

namespace App\Http\Controllers;

use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MagicLoginController extends Controller
{
    public function login(Request $request, $token)
    {
        // Find the login token
        $loginToken = LoginToken::where('token', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$loginToken) {
            return redirect()->route('login')
                ->with('error', 'Invalid or expired login token.');
        }

        // Get the user
        $user = User::find($loginToken->user_id);
        
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'User not found.');
        }

        // Log the user in
        Auth::login($user);
        
        // Delete the used token
        $loginToken->delete();
        
        // Redirect to the intended page or dashboard
        return redirect()->intended(route('filament.admin.pages.dashboard'));
    }
}