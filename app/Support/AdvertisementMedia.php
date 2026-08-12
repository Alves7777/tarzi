<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Facades\Storage;

/**
 * Armazenamento de mídia dos anúncios (S3 / MinIO ou disco local em dev).
 */
final class AdvertisementMedia
{
    public static function disk(): string
    {
        return (string) config('signage.media_disk', 's3');
    }

    public static function directory(): string
    {
        return (string) config('signage.media_directory', 'advertisements');
    }

    public static function url(?string $path): string
    {
        if (blank($path)) {
            return '';
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        $relative = ltrim($path, '/');

        if (self::shouldProxy()) {
            return rtrim((string) config('app.url'), '/').'/api/v1/media/'.$relative;
        }

        return Storage::disk(self::disk())->url($relative);
    }

    public static function shouldProxy(): bool
    {
        return (bool) config('signage.media_proxy', self::disk() === 's3');
    }

    public static function exists(?string $path): bool
    {
        if (blank($path)) {
            return false;
        }

        try {
            return Storage::disk(self::disk())->exists(ltrim($path, '/'));
        } catch (\Throwable) {
            return self::disk() === 's3';
        }
    }

    public static function read(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        try {
            return Storage::disk(self::disk())->get(ltrim($path, '/'));
        } catch (\Throwable) {
            return null;
        }
    }

    public static function mimeType(?string $path): ?string
    {
        if (blank($path)) {
            return null;
        }

        try {
            return Storage::disk(self::disk())->mimeType(ltrim($path, '/'));
        } catch (\Throwable) {
            return null;
        }
    }
}
