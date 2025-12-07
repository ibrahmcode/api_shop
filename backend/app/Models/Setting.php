<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description'
    ];

    // Cache settings for better performance
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            return $setting ? $setting->value : $default;
        });
    }

    public static function set(string $key, $value): void
    {
        self::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
        
        Cache::forget("setting_{$key}");
    }

    public static function getByGroup(string $group): array
    {
        return Cache::remember("settings_group_{$group}", 3600, function () use ($group) {
            return self::where('group', $group)->get()->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->getFormattedValue()];
            })->toArray();
        });
    }

    public static function getAllSettings(): array
    {
        return Cache::remember('all_settings', 3600, function () {
            return self::all()->mapWithKeys(function ($setting) {
                return [$setting->key => $setting->getFormattedValue()];
            })->toArray();
        });
    }

    public static function clearCache(): void
    {
        Cache::flush();
    }

    public function getFormattedValue()
    {
        return match ($this->type) {
            'boolean' => (bool) $this->value,
            'number' => is_numeric($this->value) ? (float) $this->value : $this->value,
            'json' => json_decode($this->value, true),
            'image' => $this->value ? Storage::url($this->value) : null,
            default => $this->value,
        };
    }
}
