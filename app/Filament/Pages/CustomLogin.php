<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Auth\Login;
use Filament\Notifications\Notification;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Support\Facades\Auth;


class CustomLogin extends Login
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label(' Please Enter Your Email Address')
                    ->email()
                    ->required()
                    ->autofocus(),
            ])
            ->statePath('data');
    }


    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'email' => $data['email'],
            'password' => 'password', // empty string prevents undefined error
        ];
    }

    public function handleAuthentication(array $data): LoginResponse
    {
        $user = User::where('email', $data['email'])->first();

        if (! $user) {
            Notification::make()
                ->danger()
                ->title('Login failed')
                ->body('No user found with this email.')
                ->send();

            abort(401); // Stop here
        }

        // Log user in manually (no password check)
        Auth::guard('web')->login($user);

        return app(LoginResponse::class);
    }
}
