<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $username = trim((string) $request->input('username'));
        $password = (string) $request->input('password');

        /** @var \App\Models\User|null $user */
        $user = User::where('username', $username)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            $msg = '<div class="alert alert-danger">Invalid username or password.</div>';
            return back()->withInput()->with('message', $msg);
        }

        // IMPORTANT: always log in directly, ignore 2FA for now
        Auth::login($user, $request->boolean('remember'));

        // Preserve your existing session keys for middleware / views
        session([
            'user' => $user->username,
            'role' => $user->role,
        ]);

        // Optional success message
        session()->flash(
            'message',
            '<div class="alert alert-success">Login successful. Welcome, ' . e($user->username) . '!</div>'
        );

        // Go to your home route
        return redirect()->intended('/products');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.form');
    }
}