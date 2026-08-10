<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Setting;
use InvalidArgumentException;

/**
 * Reads and writes the appearance of the login screen.
 *
 * The login screen is public, so these settings are global rather than stored
 * per user like `App\Support\UiPreferences`. Values are sanitised against
 * `config/login-screen.php` on both read and write, because the colour ends up
 * driving the panel palette and the layout ends up in a CSS class.
 *
 * @phpstan-type LoginAppearanceValues array{layout: string, color: string}
 */
final class LoginAppearance
{
    public const LAYOUT = 'layout';

    public const COLOR = 'color';

    /**
     * Prefix that keeps these apart from any other setting in the table.
     */
    private const PREFIX = 'login.';

    /**
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return [self::LAYOUT, self::COLOR];
    }

    /**
     * Every setting, sanitised and backfilled with the configured defaults.
     *
     * @return LoginAppearanceValues
     */
    public static function all(): array
    {
        $stored = Setting::allValues();

        $settings = [];

        foreach (self::keys() as $key) {
            $settings[$key] = self::sanitize($key, $stored[self::PREFIX.$key] ?? null);
        }

        /** @var LoginAppearanceValues $settings */
        return $settings;
    }

    public static function layout(): string
    {
        return (string) self::all()[self::LAYOUT];
    }

    public static function color(): string
    {
        return (string) self::all()[self::COLOR];
    }

    /**
     * Whether the login screen shows the illustrated side panel.
     */
    public static function isSplit(): bool
    {
        return self::layout() !== 'default';
    }

    /**
     * Persist several settings in a single write.
     *
     * @param  array<string, mixed>  $settings
     */
    public static function fill(array $settings): void
    {
        $values = [];

        foreach ($settings as $key => $value) {
            self::assertKnownKey($key);

            $values[self::PREFIX.$key] = self::sanitize($key, $value);
        }

        Setting::write($values);
    }

    public static function default(string $key): mixed
    {
        self::assertKnownKey($key);

        $configured = config("login-screen.defaults.{$key}");

        return match ($key) {
            self::LAYOUT => self::firstOf(self::layouts(), $configured, 'default'),
            self::COLOR => self::matchColor($configured) ?? '#6366f1',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function layouts(): array
    {
        $layouts = config('login-screen.layouts', []);

        return is_array($layouts) ? array_values(array_filter($layouts, is_string(...))) : [];
    }

    /**
     * The palette is shared with the panel switcher, so both offer the same
     * swatches and a single list has to be maintained.
     *
     * @return array<string, string>
     */
    public static function colors(): array
    {
        return UiPreferences::colors();
    }

    private static function sanitize(string $key, mixed $value): mixed
    {
        return match ($key) {
            self::LAYOUT => self::firstOf(self::layouts(), $value, self::default(self::LAYOUT)),
            self::COLOR => self::matchColor($value) ?? self::default(self::COLOR),
            default => null,
        };
    }

    /**
     * Resolve a hex value against the configured palette, ignoring case.
     */
    private static function matchColor(mixed $value): ?string
    {
        if (! is_string($value)) {
            return null;
        }

        foreach (self::colors() as $hex) {
            if (strcasecmp($hex, $value) === 0) {
                return $hex;
            }
        }

        return null;
    }

    /**
     * @param  array<int, string>  $allowed
     */
    private static function firstOf(array $allowed, mixed $value, mixed $default): mixed
    {
        return (is_string($value) && in_array($value, $allowed, true)) ? $value : $default;
    }

    private static function assertKnownKey(string $key): void
    {
        if (! in_array($key, self::keys(), true)) {
            throw new InvalidArgumentException("Unknown login appearance setting [{$key}].");
        }
    }
}
