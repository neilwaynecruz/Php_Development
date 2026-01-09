<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class LowStockService
{
    private \App\Services\SettingsService $settings;

    public function __construct(\App\Services\SettingsService $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Count how many products are low stock, based on the global setting.
     */
    public function count(): int
    {
        $threshold = $this->settings->getInt('low_stock_threshold', 10);

        return (int) DB::table('products')
            ->where('quantity', '<=', $threshold)
            ->count();
    }

    /**
     * Get the current global low-stock threshold.
     */
    public function threshold(): int
    {
        return $this->settings->getInt('low_stock_threshold', 10);
    }
}