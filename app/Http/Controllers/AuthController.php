<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        
        $credentials = $request->only('email', 'password');

        // Attempt to log the user in
        if (Auth::attempt($credentials, $request->has('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended('/app'); // Redirect to intended page or dashboard
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
    }
}
