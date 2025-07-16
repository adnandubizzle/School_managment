<?php

namespace App\Http\Controllers;

use App\Jobs\SendBulkInviteJob;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class BulkInviteController extends Controller
{
    public function showForm($school)
    {
        $school = School::findOrFail($school);
        /** @var \App\Models\User $user */

        $user = Auth::user();
        if (!$user || !$user->schools()->where('school_id', $school->id)->wherePivotIn('role', ['owner', 'admin'])->exists()) {
            abort(403, 'You do not have permission to access this page.');
        }
        return view('bulk-invite', ['school' => $school]);
    }

    public function handleUpload(Request $request, $school)
    {
        $school = School::findOrFail($school);
        /** @var \App\Models\User $user */

        $user = Auth::user();
        if (!$user || !$user->schools()->where('school_id', $school->id)->wherePivotIn('role', ['owner', 'admin'])->exists()) {
            abort(403, 'You do not have permission to access this page.');
        }
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
        ]);
        $path = $request->file('csv_file')->store('bulk-invites');
        $file = Storage::get($path);
        $rows = array_map('str_getcsv', explode("\n", $file));
        // dd($rows);
        $header = array_map('trim', array_map('strtolower', $rows[0] ?? []));
        $emailIdx = array_search('email', $header);
        $roleIdx = array_search('role', $header);
        if ($emailIdx === false || $roleIdx === false) {
            return back()->with('error', 'CSV must have email and role columns.');
        }
        $count = 0;
        foreach (array_slice($rows, 1) as $row) {
            if (count($row) < max($emailIdx, $roleIdx) + 1) continue;
            $email = trim($row[$emailIdx]);
            $role = trim($row[$roleIdx]);
            $validator = Validator::make([
                'email' => $email,
                'role' => $role,
            ], [
                'email' => 'required|email',
                'role' => 'required|in:admin,teacher,student',
            ]);
            if ($validator->fails()) continue;
            SendBulkInviteJob::dispatch($school->id, $email, $role, $user->id);
            $count++;
        }
        return back()->with('success', "$count invites queued!");
    }
} 