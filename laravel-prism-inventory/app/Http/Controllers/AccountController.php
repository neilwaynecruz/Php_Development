<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AccountController extends Controller
{
    public function form()
    {
        return view('account.form');
    }

    public function changePassword(Request $request)
    {
        $username = (string) session('user');
        $current  = trim((string)$request->input('current', ''));
        $new      = trim((string)$request->input('new', ''));
        $confirm  = trim((string)$request->input('confirm', ''));

        $errors = [];
        if ($current === '') $errors[] = "Current password is required.";
        if ($new === '') $errors[] = "New password is required.";
        if ($confirm === '') $errors[] = "Confirm new password is required.";
        if ($new !== '' && strlen($new) < 6) $errors[] = "New password must be at least 6 characters.";
        if ($new !== '' && $confirm !== '' && $new !== $confirm) $errors[] = "New passwords do not match.";

        if ($errors) {
            session()->flash('message', '<div class="alert alert-danger">'.implode("<br>", $errors).'</div>');
            return back();
        }

        $row = DB::table('users')->where('username', $username)->first();
        if (!$row) {
            session()->flash('message', '<div class="alert alert-danger">Account not found.</div>');
            return back();
        }

        $stored = $row->password;
        $ok = password_verify($current, $stored) || $stored === $current;
        if (!$ok) {
            session()->flash('message', '<div class="alert alert-danger">Current password is incorrect.</div>');
            return back();
        }
        if ($current === $new) {
            session()->flash('message', '<div class="alert alert-warning">New password must be different from current password.</div>');
            return back();
        }

        DB::table('users')->where('username', $username)->update(['password' => password_hash($new, PASSWORD_DEFAULT)]);
        session()->flash('message', '<div class="alert alert-success">Password updated successfully.</div>');
        return back();
    }
}