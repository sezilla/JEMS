<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        // If you’re using the same users table, this will work:
        if (Auth::attempt($credentials)) {
            // You can check if the user is actually an admin here
            // (optional depending on your system)
            return redirect()->intended('/admin/dashboard');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput();
        

        
    }
}
