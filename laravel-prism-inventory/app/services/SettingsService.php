<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class SettingsService
{
    private const CACHE_KEY = 'settings.all';
    private const TTL = 600; // seconds

    public function all(): array
    {
        return Cache::remember(self::CACHE_KEY, self::TTL, function () {
            return DB::table('settings')->pluck('value', 'key')->toArray();
        });
    }

    public function get(string $key, $default = null)
    {
        $all = $this->all();
        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public function getInt(string $key, int $default = 0): int
    {
        return (int) $this->get($key, $default);
    }

    public function getBool(string $key, bool $default = false): bool
    {
        $v = $this->get($key, $default ? '1' : '0');
        if (is_bool($v)) return $v;
        return in_array((string)$v, ['1','true','on','yes'], true);
    }

    public function set(string $key, $value): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            ['value' => (string)$value, 'updated_at' => now(), 'created_at' => now()]
        );
        $this->forgetCache();
    }

    public function setMany(array $assoc): void
    {
        foreach ($assoc as $k => $v) {
            DB::table('settings')->updateOrInsert(
                ['key' => (string)$k],
                ['value' => (string)$v, 'updated_at' => now(), 'created_at' => now()]
            );
        }
        $this->forgetCache();
    }

    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}