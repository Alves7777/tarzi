<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Application wide settings, stored as a key/value pair.
 *
 * Unlike the per-user UI preferences, these apply to every visitor, including
 * guests on the login screen. The whole table is small enough to be cached as a
 * single array and read from there on every request.
 */
class Setting extends Model
{
    private const CACHE_KEY = 'settings.all';

    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'json',
        ];
    }

    public static function read(string $key, mixed $default = null): mixed
    {
        return self::allValues()[$key] ?? $default;
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public static function write(array $values): void
    {
        foreach ($values as $key => $value) {
            self::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        self::forgetCache();
    }

    /**
     * @return array<string, mixed>
     */
    public static function allValues(): array
    {
        $cached = Cache::get(self::CACHE_KEY);

        if (is_array($cached)) {
            return $cached;
        }

        // The table is missing while the application is being installed, and
        // during the migration run itself. The empty result is not cached, so
        // the first request after the migration reads the real values.
        if (! Schema::hasTable((new self)->getTable())) {
            return [];
        }

        $values = self::query()->pluck('value', 'key')->all();

        Cache::forever(self::CACHE_KEY, $values);

        return $values;
    }

    public static function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected static function booted(): void
    {
        static::saved(function (): void {
            self::forgetCache();
        });

        static::deleted(function (): void {
            self::forgetCache();
        });
    }
}
