<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\AccountController;
use App\Http\Middleware\EnsureAuthenticated;
use App\Http\Middleware\EnsureAdmin;

Route::get('/', fn () => redirect()->route('login.form'));

/* Auth */
Route::get('/login', [AuthController::class, 'loginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'registerForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::match(['get','post'], '/logout', [AuthController::class, 'logout'])->name('logout');

/* Protected (logged-in) */
Route::middleware([EnsureAuthenticated::class])->group(function () {
    Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
    Route::post('/products/create', [ProductsController::class, 'create'])->name('products.create');
    Route::post('/products/update', [ProductsController::class, 'update'])->name('products.update');
    Route::post('/products/archive', [ProductsController::class, 'archive'])->name('products.archive');
    Route::post('/products/restore', [ProductsController::class, 'restore'])->name('products.restore');
    Route::post('/products/reset', [ProductsController::class, 'reset'])->name('products.reset');
    Route::post('/products/delete-permanent', [ProductsController::class, 'deletePermanent'])->name('products.deletePermanent');
    Route::post('/products/search', [ProductsController::class, 'search'])->name('products.search');

    // Account
    Route::get('/account', [AccountController::class, 'form'])->name('account.form');
    Route::post('/account/change-password', [AccountController::class, 'changePassword'])->name('account.changePassword');

    // Export Products CSV
    Route::get('/products/export', [ProductsController::class, 'exportCsv'])->name('products.exportCsv');

    /* Admin-only */
    Route::middleware([EnsureAdmin::class])->group(function () {
        Route::get('/users', [UsersController::class, 'index'])->name('users.index');
        Route::post('/users/create', [UsersController::class, 'create'])->name('users.create');
        Route::post('/users/change-role', [UsersController::class, 'changeRole'])->name('users.changeRole');
        Route::post('/users/change-pass', [UsersController::class, 'changePass'])->name('users.changePass');
        Route::post('/users/delete', [UsersController::class, 'delete'])->name('users.delete');

        // Allow both GET and POST for logs listing (filter form posts here)
        Route::match(['get','post'], '/logs', [LogsController::class, 'index'])->name('logs.index');

        // Export Logs CSV
        Route::get('/logs/export', [LogsController::class, 'exportCsv'])->name('logs.exportCsv');

        // System Settings
        Route::get('/settings', [\App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [\App\Http\Controllers\SettingsController::class, 'save'])->name('settings.save');
    });
});