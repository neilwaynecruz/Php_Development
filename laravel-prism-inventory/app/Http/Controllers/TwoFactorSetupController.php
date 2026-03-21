<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwoFactorSetupController extends Controller
{
    /**
     * Show a dedicated 2FA setup screen with QR code and recovery codes.
     * Requires the user to have already enabled 2FA.
     */
    public function show(Request $request)
    {
        $user = $request->user();

        if (! $user || ! $user->hasTwoFactorEnabled()) {
            return redirect()
                ->route('account.form')
                ->with('error', 'Enable two-factor authentication first.');
        }

        // Provided by Laravel\Fortify\TwoFactorAuthenticatable
        $qrSvg = $user->twoFactorQrCodeSvg();
        $recoveryCodes = $user->recoveryCodes();

        return view('account.two-factor-setup', [
            'qrSvg'         => $qrSvg,
            'recoveryCodes' => $recoveryCodes,
        ]);
    }
}