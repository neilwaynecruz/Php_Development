<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\PasswordResetLinkMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    public function showLinkRequestForm()
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
        ]);

        $identifier = trim((string) $request->input('identifier'));

        // Allow either username or email
        $user = User::query()
            ->where('username', $identifier)
            ->orWhere('email', $identifier)
            ->first();

        if (! $user || ! $user->email) {
            $msg = '<div class="alert alert-danger">We could not find a user with that username/email or no email is set.</div>';
            return back()->withInput()->with('message', $msg);
        }

        $token = Str::random(64);

        // Store token in password_resets
        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            [
                'token'      => hash('sha256', $token),
                'created_at' => now(),
            ]
        );

        // Send email with raw token
        Mail::to($user->email)->send(new PasswordResetLinkMail($user, $token));

        $msg = '<div class="alert alert-success">If that account exists, a password reset link has been emailed.</div>';
        return back()->with('message', $msg);
    }
}