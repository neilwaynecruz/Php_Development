<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (session()->has('user')) return redirect()->route('products.index');
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $u = trim((string)$request->input('username', ''));
        $p = trim((string)$request->input('password', ''));

        // Preserve original validation messages
        $errors = [];
        if ($u === '') $errors[] = "Username is required.";
        if ($p === '') $errors[] = "Password is required.";
        if ($errors) {
            session()->flash('message', '<div class="alert alert-danger">'.implode("<br>", $errors).'</div>');
            return back();
        }

        // Lookup and plaintext fallback (to match current behavior)
        $row = DB::table('users')->where('username', $u)->first();
        $valid = false; $role = 'user';
        if ($row) {
            $stored = $row->password;
            if (password_verify($p, $stored) || $stored === $p) {
                $valid = true;
                $role = $row->role ?? 'user';
            }
        }

        if ($valid) {
            session()->regenerate();
            session(['user' => $u, 'role' => $role]);
            DB::table('activity_logs')->insert(['username' => $u, 'action' => 'login', 'product_id' => null, 'details' => null]);
            session()->flash('message', '<div class="alert alert-success">Login successful. Welcome, '.e($u).'!</div>');
            return redirect()->route('products.index');
        }

        session()->flash('message', '<div class="alert alert-danger">Invalid username or password.</div>');
        return back();
    }

    public function registerForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $u = trim((string)$request->input('username',''));
        $p = trim((string)$request->input('password',''));
        $cp= trim((string)$request->input('confirm',''));

        $errors = [];
        if ($u === '') $errors[] = "Username is required.";
        if ($p === '') $errors[] = "Password is required.";
        if ($cp=== '') $errors[] = "Confirm Password is required.";
        if ($u !== '' && strlen($u) < 4) $errors[] = "Username must be at least 4 characters.";
        if ($p !== '' && strlen($p) < 6) $errors[] = "Password must be at least 6 characters.";
        if ($p !== '' && $cp !== '' && $p !== $cp) $errors[] = "Passwords do not match.";

        $exists = DB::table('users')->where('username', $u)->exists();
        if ($exists) $errors[] = "Username is already taken.";

        if ($errors) {
            session()->flash('message', '<div class="alert alert-danger">'.implode("<br>", $errors).'</div>');
            return back();
        }

        DB::table('users')->insert([
            'username' => $u,
            'password' => password_hash($p, PASSWORD_DEFAULT),
            'role' => 'user',
        ]);

        session()->flash('message', '<div class="alert alert-success">Account created! You can now login.</div>');
        return redirect()->route('login.form');
    }

    public function logout(Request $request)
    {
        if (session()->has('user')) {
            $u = (string) session('user');
            DB::table('activity_logs')->insert(['username' => $u, 'action' => 'logout', 'product_id' => null, 'details' => null]);
        }
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login.form');
    }
}