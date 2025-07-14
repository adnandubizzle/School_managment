<?php

namespace App\Http\Controllers;

use App\Models\LoginToken;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MagicController extends Controller
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

        // token mil gya h Valid!
        //ab dekhy ga k user h?
        $user = User::find($loginToken->user_id);
        
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'User not found.');
        }
//logout current user

Auth::guard('web')->logout();

        // Log the user in
        
Auth::guard('web')->login($user);

        
        // Delete the used token
        $loginToken->delete();
        
        // Redirect to the intended page or dashboard
return redirect('/admin') ; 
 } 
}