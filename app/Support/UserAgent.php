<?php

declare(strict_types=1);

namespace App\Support;

use Illuminate\Support\Str;
use Stringable;

/**
 * Minimal browser and platform detection for the session lists.
 *
 * User agent strings are deliberately misleading — every browser claims to be
 * several others — so the order of the checks below matters: the most specific
 * token wins. This is only ever used to help a person recognise their own
 * device, never for anything security related.
 */
final class UserAgent implements Stringable
{
    private function __construct(
        public readonly ?string $browser,
        public readonly ?string $platform,
    ) {}

    public static function parse(?string $userAgent): self
    {
        $userAgent = trim((string) $userAgent);

        if ($userAgent === '') {
            return new self(null, null);
        }

        return new self(
            self::detectBrowser($userAgent),
            self::detectPlatform($userAgent),
        );
    }

    public function __toString(): string
    {
        return $this->describe();
    }

    /**
     * A human readable label, e.g. "Chrome on macOS".
     */
    public function describe(): string
    {
        return match (true) {
            filled($this->browser) && filled($this->platform) => __('sessions.device_description', [
                'browser' => $this->browser,
                'platform' => $this->platform,
            ]),
            filled($this->browser) => $this->browser,
            filled($this->platform) => $this->platform,
            default => __('sessions.unknown_device'),
        };
    }

    private static function detectBrowser(string $userAgent): ?string
    {
        // Edge and Opera masquerade as Chrome, and Chrome masquerades as Safari.
        return match (true) {
            Str::contains($userAgent, ['Edg/', 'Edge/', 'EdgA/', 'EdgiOS/']) => 'Edge',
            Str::contains($userAgent, ['OPR/', 'Opera']) => 'Opera',
            Str::contains($userAgent, ['SamsungBrowser/']) => 'Samsung Internet',
            Str::contains($userAgent, ['CriOS/', 'Chrome/', 'Chromium/']) => 'Chrome',
            Str::contains($userAgent, ['FxiOS/', 'Firefox/']) => 'Firefox',
            Str::contains($userAgent, 'Safari/') => 'Safari',
            default => null,
        };
    }

    private static function detectPlatform(string $userAgent): ?string
    {
        return match (true) {
            Str::contains($userAgent, ['iPhone', 'iPad', 'iPod']) => 'iOS',
            Str::contains($userAgent, 'Android') => 'Android',
            Str::contains($userAgent, 'Windows') => 'Windows',
            // Checked after iOS, which also reports "like Mac OS X".
            Str::contains($userAgent, ['Macintosh', 'Mac OS X']) => 'macOS',
            Str::contains($userAgent, ['CrOS']) => 'ChromeOS',
            Str::contains($userAgent, 'Linux') => 'Linux',
            default => null,
        };
    }
}
