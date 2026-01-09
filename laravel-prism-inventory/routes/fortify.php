<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Http\Controllers\ConfirmedTwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\RecoveryCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorAuthenticationController;
use Laravel\Fortify\Http\Controllers\TwoFactorQrCodeController;
use Laravel\Fortify\Http\Controllers\TwoFactorSecretKeyController;

/*
|--------------------------------------------------------------------------
| Fortify Two-Factor Routes
|--------------------------------------------------------------------------
|
| These are the standard Fortify 2FA routes. They are protected by the
| "auth" middleware – so Auth::check() must be true.
|
*/

Route::middleware(['web', 'auth'])->group(function () {
    // Enable / disable 2FA
    Route::post('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'store'])
        ->name('two-factor.enable');

    Route::delete('/user/two-factor-authentication', [TwoFactorAuthenticationController::class, 'destroy'])
        ->name('two-factor.disable');

    // Regenerate recovery codes
    Route::post('/user/two-factor-recovery-codes', [RecoveryCodeController::class, 'store'])
        ->name('two-factor.recovery-codes');

    // (Optional) endpoints for QR code / secret key if you want to show them:
    Route::get('/user/two-factor-qr-code', [TwoFactorQrCodeController::class, 'show'])
        ->name('two-factor.qr-code');

    Route::get('/user/two-factor-secret-key', [TwoFactorSecretKeyController::class, 'show'])
        ->name('two-factor.secret-key');

    // Confirmed 2FA enabling if using confirm flow
    Route::post('/user/confirmed-two-factor-authentication', [ConfirmedTwoFactorAuthenticationController::class, 'store'])
        ->name('two-factor.confirmed');
});