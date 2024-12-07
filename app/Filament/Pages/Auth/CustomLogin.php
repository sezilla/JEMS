<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Radio;
use Filament\Forms\Form;
use Filament\Pages\Auth\Login as BaseLogin;
use App\Http\Responses\Auth\CustomLoginResponse;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;


class CustomLogin extends BaseLogin
{
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('email')
                    ->label('Email Address')
                    ->email()
                    ->required(),
                TextInput::make('password')
                    ->hint(filament()->hasPasswordReset() ? new HtmlString(Blade::render('<x-filament::link :href="filament()->getRequestPasswordResetUrl()" tabindex="3"> {{ __(\'filament-panels::pages/auth/login.actions.request_password_reset.label\') }}</x-filament::link>')) : null)
                    ->label('Password')
                    ->password()
                    ->minLength(8)
                    ->revealable()
                    ->required(),
                Checkbox::make('remember')
                    ->label('Remember Me'),
                Radio::make('panel')
                    ->required()
                    ->label('Login as?')
                    ->inline()
                    ->inlineLabel(false)
                    ->options([
                        'Admin' => 'Admin',
                        'Staff' => 'Staff',
                    ]),
            ]);
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        $credentials = [
            'email' => $data['email'],
            'password' => $data['password'],
        ];

        if (Auth::attempt($credentials, $data['remember'] ?? false)) {
            // Authentication successful
            $panel = $data['panel'];

            if ($panel === 'Admin') {
                return new CustomLoginResponse('/admin');
            } elseif ($panel === 'Staff') {
                return new CustomLoginResponse('/app');
            }
        }

        // Authentication failed
        $this->addError('email', __('auth.failed'));
        return null;
    }
}
