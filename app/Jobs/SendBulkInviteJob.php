<?php

namespace App\Jobs;

use App\Models\LoginToken;
use App\Models\School;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendBulkInviteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $schoolId;
    protected $email;
    protected $role;
    protected $invitedBy;

    public function __construct($schoolId, $email, $role, $invitedBy)
    {
        $this->schoolId = $schoolId;
        $this->email = $email;
        $this->role = $role;
        $this->invitedBy = $invitedBy;
    }

    public function handle()
    {
        $user = User::firstOrCreate(
            ['email' => $this->email],
            [
                'name' => explode('@', $this->email)[0],
                'password' => bcrypt('password')
            ]
        );

        $school = School::findOrFail($this->schoolId);
        $school->users()->syncWithoutDetaching([
            $user->id => [
                'role' => $this->role,
                'invited_by' => $this->invitedBy,
            ]
        ]);

        $token = Str::random(60);
        LoginToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addMinutes(5),
        ]);

        Mail::raw("Click here to log in: " . route('magic.login', $token), function ($message) {
            $message->to($this->email)->subject('Your Magic Login Link');
        });
    }
} 