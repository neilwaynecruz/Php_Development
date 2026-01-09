<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ResetPasswordController extends Controller
{
    public function showResetForm(string $token)
    {
        return view('auth.reset-password', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email'                 => 'required|email',
            'password'              => 'required|confirmed|min:8',
            'token'                 => 'required|string',
        ]);

        $email = (string) $request->input('email');
        $token = (string) $request->input('token');

        $row = DB::table('password_resets')
            ->where('email', $email)
            ->first();

        if (! $row) {
            $msg = '<div class="alert alert-danger">This password reset link is invalid or has already been used.</div>';
            return back()->withInput()->with('message', $msg);
        }

        // Compare hashed token
        if (! hash_equals($row->token, hash('sha256', $token))) {
            $msg = '<div class="alert alert-danger">This password reset link is invalid or has expired.</div>';
            return back()->withInput()->with('message', $msg);
        }

        // Optional expiration (e.g. 60 minutes)
        $created = $row->created_at ? now()->parse($row->created_at) : null;
        if ($created && $created->diffInMinutes(now()) > 60) {
            DB::table('password_resets')->where('email', $email)->delete();
            $msg = '<div class="alert alert-danger">This password reset link has expired. Please request a new one.</div>';
            return redirect()->route('password.request')->with('message', $msg);
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            $msg = '<div class="alert alert-danger">We could not find a user with that email.</div>';
            return back()->withInput()->with('message', $msg);
        }

        // Update password
        $user->password = Hash::make($request->input('password'));
        $user->save();

        // Delete reset row so link cannot be reused
        DB::table('password_resets')->where('email', $email)->delete();

        $msg = '<div class="alert alert-success">Your password has been reset. You can now log in.</div>';
        return redirect()->route('login.form')->with('message', $msg);
    }
}