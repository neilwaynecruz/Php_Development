<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * LEGACY login / logout methods are no longer used.
     * Login is handled by App\Http\Controllers\Auth\LoginController.
     * We keep these empty so any old references won't crash.
     */
    public function loginForm() {}
    public function login() {}
    public function logout() {}

    /**
     * Show the registration form.
     */
    public function registerForm()
    {
        return view('auth.register');
    }

    /**
     * Handle new user registration.
     */
    public function register(Request $request)
    {
        // Validation (mirrors your old messages but with email + confirmation)
        $request->validate(
            [
                'username' => ['required', 'string', 'min:4', 'max:255', 'unique:users,username'],
                'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ],
            [
                'username.required' => 'Username is required.',
                'username.min'      => 'Username must be at least 4 characters.',
                'username.unique'   => 'Username is already taken.',
                'email.required'    => 'Email is required.',
                'email.email'       => 'Please enter a valid email.',
                'email.unique'      => 'Email is already in use.',
                'password.required' => 'Password is required.',
                'password.min'      => 'Password must be at least 6 characters.',
                'password.confirmed'=> 'Passwords do not match.',
            ]
        );

        $username = trim((string) $request->input('username'));
        $email    = trim((string) $request->input('email'));

        // Create user using Eloquent + bcrypt
        $user = User::create([
            'username' => $username,
            'email'    => $email,
            'password' => Hash::make($request->input('password')),
            'role'     => 'user', // default role
        ]);

        // Auto-login after registration using Laravel's auth guard
        Auth::login($user);

        // Preserve your existing session('user') / session('role')
        session([
            'user' => $user->username,
            'role' => $user->role,
        ]);

        session()->flash(
            'message',
            '<div class="alert alert-success">Account created! You are now logged in.</div>'
        );

        return redirect()->route('products.index');
    }
}