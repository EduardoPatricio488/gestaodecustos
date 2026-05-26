<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, $default = null)
    {
        return Cache::remember('setting_' . $key, 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, $value)
    {
        $setting = self::updateOrCreate(
            ['key' => $key],
            ['value' => (string) $value]
        );

        Cache::forget('setting_' . $key);
        if ($key === 'maintenance_mode') {
            Cache::forget('setting_maintenance_mode');
        }

        return $setting;
    }
}
