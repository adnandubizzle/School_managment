<?php

namespace App\Filament\Resources\SchoolResource\Pages;

use App\Models\LoginToken;
use App\Models\School;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Form;

class InviteUser extends Page
{
    protected static ?string $title = 'Invite User';

    protected static string $resource = \App\Filament\Resources\SchoolResource::class;

    protected static string $view = 'filament.resources.school-resource.pages.invite-user';

    public $record = null;

    public $email = '';
    public $role = '';

    public function mount($record)
    {
        // Always resolve $record to a School model
        $school = \App\Models\School::findOrFail($record);
        if (!$this->canUserAccess($school)) {
            abort(403, 'You do not have permission to access this page.');
        }
        $this->record = $school;
    }

    protected function canUserAccess($school)
    {
                    /** @var \App\Models\User|null $user */

        $user = Auth::user();
        
        if (!$user) {
            return false;
        }

        return $user->schools()
            ->where('school_id', $school->id)
            ->wherePivotIn('role', ['owner', 'admin'])
            ->exists();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required(),
                Forms\Components\Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'teacher' => 'Teacher',
                        'student' => 'Student',
                    ])
                    ->required(),
            ]);
    }

    public function sendInvite(): void
    {
$this->validate([
    'email' => 'required|email:dns',  //dns uses to see is its accurate domain namee
    'role' => 'required|in:admin,teacher,student',
], [
    'email.email' => 'The email format is invalid.',
    'email.dns' => 'This email domain cannot receive emails. Please check for typos.',
]);


        $user = User::firstOrCreate(
            ['email' => $this->email],
            [
                'name' => explode('@', $this->email)[0],
                'password' => bcrypt('password') 
            ]
        );

        $this->record->users()->syncWithoutDetaching([
            $user->id => [
                'role' => $this->role,
                'invited_by' => Auth::id(),
            ]
        ]);

        $token = Str::random(60);
        LoginToken::create([
            'user_id' => $user->id,
            'token' => $token,
            'expires_at' => now()->addMinutes(5), //added an expiry that of 5 min from current timeStamop
        ]);

        Mail::raw("Click here to log in: " . route('magic.login', $token), function ($message) {
            $message->to($this->email)->subject('Your Magic Login Link');
        });


        Notification::make()
            ->title('Invite sent!')
            ->success()
            ->send();


        // Clear form after sending

        $this->email = '';
        $this->role = '';
    }


    protected function getFormActions(): array
    {
        return [
            Action::make('sendInvite')
                ->label('Send Invite')
                ->action('sendInvite'),
        ];
    }
}