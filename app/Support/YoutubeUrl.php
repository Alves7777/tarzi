<?php

namespace App\Support;

final class YoutubeUrl
{
    public static function isYoutube(?string $url): bool
    {
        return self::extractVideoId($url) !== null;
    }

    public static function extractVideoId(?string $url): ?string
    {
        if (blank($url)) {
            return null;
        }

        if (preg_match('/^[a-zA-Z0-9_-]{11}$/', $url)) {
            return $url;
        }

        $parsed = parse_url($url);

        if ($parsed === false) {
            return null;
        }

        $host = strtolower($parsed['host'] ?? '');

        if (str_contains($host, 'youtu.be')) {
            $path = trim($parsed['path'] ?? '', '/');

            return preg_match('/^[a-zA-Z0-9_-]{11}$/', $path) ? $path : null;
        }

        if (str_contains($host, 'youtube.com') || str_contains($host, 'youtube-nocookie.com')) {
            parse_str($parsed['query'] ?? '', $query);

            if (! empty($query['v']) && preg_match('/^[a-zA-Z0-9_-]{11}$/', (string) $query['v'])) {
                return $query['v'];
            }

            if (preg_match('#/(?:embed|shorts|v)/([a-zA-Z0-9_-]{11})#', $parsed['path'] ?? '', $matches)) {
                return $matches[1];
            }
        }

        return null;
    }
}
