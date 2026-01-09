<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\RequisitionsController;
use App\Http\Controllers\SettingsController;
use App\Http\Middleware\EnsureAuthenticated;
use App\Http\Middleware\EnsureAdmin;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\TwoFactorSetupController;

/*
|--------------------------------------------------------------------------
| Public / Auth Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login form
Route::get('/', fn () => redirect()->route('login.form'));

// Login form (uses new LoginController)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login.form');

// Handle login
Route::post('/login', [LoginController::class, 'login'])->name('login.post');

// Logout
Route::match(['get', 'post'], '/logout', [\App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('app.logout');

// Registration (OPTIONAL, still using your old AuthController)
Route::get('/register', [\App\Http\Controllers\AuthController::class, 'registerForm'])->name('register.form');
Route::post('/register', [\App\Http\Controllers\AuthController::class, 'register'])->name('register.post');

/*
|--------------------------------------------------------------------------
| Forgot / Reset Password (custom flow you already have)
|--------------------------------------------------------------------------
*/

// Show "Forgot Password" form
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])
    ->name('password.request');

// Handle submission and email reset link
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLink'])
    ->name('password.email');

// Show "Reset Password" form (via emailed link)
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])
    ->name('password.reset');

// Handle actual password reset
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])
    ->name('password.update');


/*
|--------------------------------------------------------------------------
| Protected Routes (logged-in users only)
|--------------------------------------------------------------------------
|
| These still use your custom EnsureAuthenticated middleware which checks
| session('user') / session('role'). We ALSO log users in via Laravel's
| Auth::login() in LoginController so Fortify / 2FA can work.
|
*/

Route::middleware([EnsureAuthenticated::class])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Products
    |--------------------------------------------------------------------------
    */
    Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
    Route::post('/products/create', [ProductsController::class, 'create'])->name('products.create');
    Route::post('/products/update', [ProductsController::class, 'update'])->name('products.update');
    Route::post('/products/archive', [ProductsController::class, 'archive'])->name('products.archive');
    Route::post('/products/restore', [ProductsController::class, 'restore'])->name('products.restore');
    Route::post('/products/reset', [ProductsController::class, 'reset'])->name('products.reset');
    Route::post('/products/delete-permanent', [ProductsController::class, 'deletePermanent'])->name('products.deletePermanent');
    Route::post('/products/search', [ProductsController::class, 'search'])->name('products.search');
    Route::get('/products/export', [ProductsController::class, 'exportCsv'])->name('products.exportCsv');

    /*
    |--------------------------------------------------------------------------
    | Account
    |--------------------------------------------------------------------------
    */
    Route::get('/account', [AccountController::class, 'form'])->name('account.form');
    Route::post('/account/change-password', [AccountController::class, 'changePassword'])->name('account.changePassword');

    // Two-Factor setup screen (QR + recovery codes)
    Route::get('/account/two-factor-setup', [TwoFactorSetupController::class, 'show'])
        ->name('account.twoFactorSetup');

    /*
    |--------------------------------------------------------------------------
    | User: My Requests
    |--------------------------------------------------------------------------
    */
    Route::get('/my-requests', [RequisitionsController::class, 'myRequests'])->name('requisitions.my.index');
    Route::post('/my-requests/create', [RequisitionsController::class, 'createDraft'])->name('requisitions.my.create');
    Route::get('/my-requests/{id}', [RequisitionsController::class, 'showMy'])->name('requisitions.my.show');
    Route::get('/my-requests/{id}/edit', [RequisitionsController::class, 'editMy'])->name('requisitions.my.edit');
    Route::post('/my-requests/{id}/items', [RequisitionsController::class, 'addItemMy'])->name('requisitions.my.items.add');
    Route::post('/my-requests/{id}/items/{itemId}/remove', [RequisitionsController::class, 'removeItemMy'])->name('requisitions.my.items.remove');
    Route::post('/my-requests/{id}/submit', [RequisitionsController::class, 'submitMy'])->name('requisitions.my.submit');
    Route::post('/my-requests/{id}/cancel', [RequisitionsController::class, 'cancelMy'])->name('requisitions.my.cancel');
    Route::post('/my-requests/{id}/notes', [RequisitionsController::class, 'saveNotesMy'])->name('requisitions.my.notes.save');

    // USER delete route – user-only, not under admin middleware
    Route::post('/my-requests/{id}/delete', [RequisitionsController::class, 'deleteMy'])->name('requisitions.my.delete');

    /*
    |--------------------------------------------------------------------------
    | Admin-only routes
    |--------------------------------------------------------------------------
    */

    Route::middleware([EnsureAdmin::class])->group(function () {

        /* Users */
        Route::get('/users', [UsersController::class, 'index'])->name('users.index');
        Route::post('/users/create', [UsersController::class, 'create'])->name('users.create');
        Route::post('/users/change-role', [UsersController::class, 'changeRole'])->name('users.changeRole');
        Route::post('/users/change-pass', [UsersController::class, 'changePass'])->name('users.changePass');
        Route::post('/users/delete', [UsersController::class, 'delete'])->name('users.delete');

        /* Requisitions (Admin) */
        Route::get('/requisitions', [RequisitionsController::class, 'adminIndex'])->name('requisitions.admin.index');
        Route::get('/requisitions/{id}', [RequisitionsController::class, 'adminShow'])->name('requisitions.admin.show');
        Route::post('/requisitions/{id}/approve', [RequisitionsController::class, 'adminApprove'])->name('requisitions.admin.approve');
        Route::post('/requisitions/{id}/reject', [RequisitionsController::class, 'adminReject'])->name('requisitions.admin.reject');
        Route::post('/requisitions/{id}/fulfill', [RequisitionsController::class, 'adminFulfill'])->name('requisitions.admin.fulfill');
        Route::post('/requisitions/{id}/delete', [RequisitionsController::class, 'adminDelete'])->name('requisitions.admin.delete');

        /* Logs */
        Route::match(['get', 'post'], '/logs', [LogsController::class, 'index'])->name('logs.index');
        Route::get('/logs/export', [LogsController::class, 'exportCsv'])->name('logs.exportCsv');

        /* Settings */
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'save'])->name('settings.save');
    });
});