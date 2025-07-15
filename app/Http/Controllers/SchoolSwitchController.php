<?php


namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;


class SchoolSwitchController extends Controller
{
    public function switch(School $school): RedirectResponse
    {

        // Ensure the user belongs to this school

    /** @var \App\Models\User|null $user */
$user = Auth::user();

if (! $user?->schools->contains($school->id)) {
    abort(403);
}

        session(['current_school_id' => $school->id]);

        return redirect()->route('filament.admin.pages.dashboard');
    }
}
