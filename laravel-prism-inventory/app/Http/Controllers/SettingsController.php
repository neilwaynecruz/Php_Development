<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\SettingsService;

class SettingsController extends Controller
{
    public function index(Request $request, SettingsService $settings)
    {
        $this->authorizeAdmin($request);

        $brandText         = (string) $settings->get('brand_text', 'PRISM');
        $lowStockThreshold = (int) $settings->get('low_stock_threshold', 10);
        $showTotalToUser   = (bool) $settings->getBool('show_total_to_user', true);

        return view('settings.index', compact('brandText', 'lowStockThreshold', 'showTotalToUser'));
    }

    public function save(Request $request, SettingsService $settings)
    {
        $this->authorizeAdmin($request);

        $data = $request->validate([
            'brand_text'           => ['required','string','max:64'],
            'low_stock_threshold'  => ['required','integer','min:0','max:1000000'],
            'show_total_to_user'   => ['nullable','in:on,1,0'],
        ]);

        $settings->setMany([
            'brand_text'          => $data['brand_text'],
            'low_stock_threshold' => (string) $data['low_stock_threshold'],
            'show_total_to_user'  => $request->boolean('show_total_to_user') ? '1' : '0',
        ]);

        $msg = '<div class="alert alert-success">Settings saved.</div>';
        if ($request->expectsJson()) return response()->json(['ok' => true, 'message' => $msg]);

        session()->flash('message', $msg);
        return redirect()->route('settings.index');
    }

    private function authorizeAdmin(Request $request): void
    {
        if ((string) $request->session()->get('role') !== 'admin') {
            abort(403, 'Only administrators can access System Settings.');
        }
    }
}