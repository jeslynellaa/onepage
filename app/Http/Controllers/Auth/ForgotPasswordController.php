<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * Display the forgot password form.
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send a password reset link.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        Password::sendResetLink(
            $request->only('email')
        );

        return back()->with(
            'status',
            "If an account exists for that email address, we've sent a secure password reset link. Please check your inbox and spam folder."
        );
    }
}