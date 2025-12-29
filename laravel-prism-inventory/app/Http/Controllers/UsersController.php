<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller
{
    public function index()
    {
        // Fetch all users (same ordering)
        $users = DB::table('users')->orderByDesc('uid')->get();
        return view('users.index', compact('users'));
    }

    public function create(Request $request)
    {
        $username = trim((string)$request->input('username',''));
        $password = trim((string)$request->input('password',''));
        $role     = trim((string)$request->input('role',''));

        $errors = [];
        if ($username === '') $errors[] = "Username is required.";
        if ($password === '') $errors[] = "Password is required.";
        if ($username !== '' && strlen($username) < 4) $errors[] = "Username must be at least 4 characters.";
        if ($password !== '' && strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
        if (!in_array($role, ['admin','user'], true)) $errors[] = "Invalid role.";

        $exists = DB::table('users')->where('username', $username)->exists();
        if ($exists) $errors[] = "Username is already taken.";

        if ($errors) {
            session()->flash('message', '<div class="alert alert-danger">'.implode("<br>", $errors).'</div>');
            return back();
        }

        DB::table('users')->insert([
            'username' => $username,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'role'     => $role,
        ]);

        session()->flash('message', '<div class="alert alert-success">User created successfully.</div>');
        return back();
    }

    public function changeRole(Request $request)
    {
        $uid  = (int) $request->input('uid');
        $role = trim((string)$request->input('role',''));
        if (!in_array($role, ['admin','user'], true)) {
            session()->flash('message', '<div class="alert alert-danger">Invalid role.</div>');
            return back();
        }
        DB::table('users')->where('uid', $uid)->update(['role' => $role]);
        session()->flash('message', '<div class="alert alert-success">Role updated.</div>');
        return back();
    }

    public function changePass(Request $request)
    {
        $uid = (int) $request->input('uid');
        $new = trim((string)$request->input('new', ''));
        if ($new === '' || strlen($new) < 6) {
            session()->flash('message', '<div class="alert alert-danger">Password must be at least 6 characters.</div>');
            return back();
        }
        DB::table('users')->where('uid', $uid)->update(['password' => password_hash($new, PASSWORD_DEFAULT)]);
        session()->flash('message', '<div class="alert alert-success">Password updated.</div>');
        return back();
    }

    public function delete(Request $request)
    {
        $uid = (int) $request->input('uid');
        $self = (string) session('user');

        // Prevent self-delete (same logic)
        $row = DB::table('users')->where('uid', $uid)->first();
        if ($row && $row->username === $self) {
            session()->flash('message', '<div class="alert alert-warning">You cannot delete your own account.</div>');
            return back();
        }

        DB::table('users')->where('uid', $uid)->delete();
        session()->flash('message', '<div class="alert alert-success">User deleted.</div>');
        return back();
    }
}