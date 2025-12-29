<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\LogsController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\RequisitionsController;
use App\Http\Controllers\SettingsController;
use App\Http\Middleware\EnsureAuthenticated;
use App\Http\Middleware\EnsureAdmin;

Route::get('/', fn () => redirect()->route('login.form'));

/* Auth */
Route::get('/login', [AuthController::class, 'loginForm'])->name('login.form');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'registerForm'])->name('register.form');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

/* Protected (logged-in) */
Route::middleware([EnsureAuthenticated::class])->group(function () {

    /* Products */
    Route::get('/products', [ProductsController::class, 'index'])->name('products.index');
    Route::post('/products/create', [ProductsController::class, 'create'])->name('products.create');
    Route::post('/products/update', [ProductsController::class, 'update'])->name('products.update');
    Route::post('/products/archive', [ProductsController::class, 'archive'])->name('products.archive');
    Route::post('/products/restore', [ProductsController::class, 'restore'])->name('products.restore');
    Route::post('/products/reset', [ProductsController::class, 'reset'])->name('products.reset');
    Route::post('/products/delete-permanent', [ProductsController::class, 'deletePermanent'])->name('products.deletePermanent');
    Route::post('/products/search', [ProductsController::class, 'search'])->name('products.search');
    Route::get('/products/export', [ProductsController::class, 'exportCsv'])->name('products.exportCsv');

    /* Account */
    Route::get('/account', [AccountController::class, 'form'])->name('account.form');
    Route::post('/account/change-password', [AccountController::class, 'changePassword'])->name('account.changePassword');

    /* USER: My Requests */
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

    /* Admin-only */
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
        // (optional) admin-side delete route could go here

        /* Logs */
        Route::match(['get', 'post'], '/logs', [LogsController::class, 'index'])->name('logs.index');
        Route::get('/logs/export', [LogsController::class, 'exportCsv'])->name('logs.exportCsv');

        /* Settings */
        Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingsController::class, 'save'])->name('settings.save');
    });
});