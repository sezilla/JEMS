<?php

namespace App\Http\Responses\Auth;

use Filament\Http\Responses\Auth\Contracts\LoginResponse as LoginResponseContract;

class CustomLoginResponse implements LoginResponseContract
{
    protected string $redirectUrl;

    public function __construct(string $redirectUrl)
    {
        $this->redirectUrl = $redirectUrl;
    }

    public function toResponse($request)
    {
        return redirect($this->redirectUrl);
    }
}
