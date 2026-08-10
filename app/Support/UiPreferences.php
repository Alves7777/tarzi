<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\Concerns\HasUiPreferences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use InvalidArgumentException;

/**
 * Reads and writes the UI preferences that drive the Filament UI switcher.
 *
 * Values are sanitised against `config/ui-switcher.php` on both read and write,
 * so a value that is not offered by the configuration can never reach the panel
 * (several of them end up inside a `<style>` tag).
 *
 * @phpstan-type UiPreferenceValues array{color: string, font: string, font_size: int, layout: string, density: string}
 */
final class UiPreferences
{
    public const COLOR = 'color';

    public const FONT = 'font';

    public const FONT_SIZE = 'font_size';

    public const LAYOUT = 'layout';

    public const DENSITY = 'density';

    /**
     * @return array<int, string>
     */
    public static function keys(): array
    {
        return [self::COLOR, self::FONT, self::FONT_SIZE, self::LAYOUT, self::DENSITY];
    }

    /**
     * Every preference, sanitised and backfilled with the configured defaults.
     *
     * @return UiPreferenceValues
     */
    public static function all(): array
    {
        $stored = self::read();

        $preferences = [];

        foreach (self::keys() as $key) {
            $preferences[$key] = self::sanitize($key, $stored[$key] ?? null);
        }

        /** @var UiPreferenceValues $preferences */
        return $preferences;
    }

    public static function get(string $key): mixed
    {
        self::assertKnownKey($key);

        return self::sanitize($key, self::read()[$key] ?? null);
    }

    public static function set(string $key, mixed $value): void
    {
        self::assertKnownKey($key);

        self::write([...self::read(), $key => self::sanitize($key, $value)]);
    }

    /**
     * Persist several preferences in a single write.
     *
     * @param  array<string, mixed>  $preferences
     */
    public static function fill(array $preferences): void
    {
        $stored = self::read();

        foreach ($preferences as $key => $value) {
            self::assertKnownKey($key);

            $stored[$key] = self::sanitize($key, $value);
        }

        self::write($stored);
    }

    /**
     * Restore every preference to its configured default.
     */
    public static function reset(): void
    {
        self::write([]);
    }

    public static function default(string $key): mixed
    {
        self::assertKnownKey($key);

        $configured = config("ui-switcher.defaults.{$key}");

        return match ($key) {
            self::COLOR => self::matchColor($configured) ?? '#6366f1',
            self::FONT => self::firstOf(self::fonts(), $configured, 'Inter'),
            self::LAYOUT => self::firstOf(self::layouts(), $configured, 'sidebar'),
            self::DENSITY => self::firstOf(array_keys(self::densities()), $configured, 'default'),
            self::FONT_SIZE => self::clampFontSize(is_numeric($configured) ? (int) $configured : 16),
            default => $configured,
        };
    }

    /**
     * Hex values offered in the color picker, keyed by their label.
     *
     * @return array<string, string>
     */
    public static function colors(): array
    {
        $colors = config('ui-switcher.colors', []);

        return is_array($colors) ? array_filter($colors, is_string(...)) : [];
    }

    /**
     * @return array<int, string>
     */
    public static function fonts(): array
    {
        $fonts = config('ui-switcher.fonts', []);

        return is_array($fonts) ? array_values(array_filter($fonts, is_string(...))) : [];
    }

    /**
     * @return array<int, string>
     */
    public static function layouts(): array
    {
        $layouts = config('ui-switcher.layouts', []);

        return is_array($layouts) ? array_values(array_filter($layouts, is_string(...))) : [];
    }

    /**
     * Density name to `--spacing` value.
     *
     * @return array<string, string>
     */
    public static function densities(): array
    {
        $densities = config('ui-switcher.densities', []);

        return is_array($densities) ? array_filter($densities, is_string(...)) : [];
    }

    /**
     * @return array{min: int, max: int}
     */
    public static function fontSizeRange(): array
    {
        $min = (int) config('ui-switcher.font_size.min', 12);
        $max = (int) config('ui-switcher.font_size.max', 20);

        return $min <= $max ? ['min' => $min, 'max' => $max] : ['min' => $max, 'max' => $min];
    }

    public static function isEnabled(): bool
    {
        return (bool) config('ui-switcher.enabled', true);
    }

    /**
     * The CSS custom properties the panel needs for the current preferences.
     *
     * @return array<string, string>
     */
    public static function cssVariables(): array
    {
        $preferences = self::all();
        $densities = self::densities();

        return array_filter([
            '--spacing' => $densities[$preferences[self::DENSITY]] ?? null,
            '--ui-switcher-font-size' => "{$preferences[self::FONT_SIZE]}px",
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private static function read(): array
    {
        $user = self::databaseUser();

        if ($user !== null) {
            return $user->getUiPreferences();
        }

        $stored = session(self::sessionKey(), []);

        return is_array($stored) ? $stored : [];
    }

    /**
     * @param  array<string, mixed>  $preferences
     */
    private static function write(array $preferences): void
    {
        $user = self::databaseUser();

        if ($user !== null) {
            $user->setUiPreferences($preferences);

            return;
        }

        session()->put(self::sessionKey(), $preferences);
    }

    /**
     * The authenticated model when the database driver is usable, otherwise null.
     */
    private static function databaseUser(): ?Model
    {
        if (config('ui-switcher.driver') !== 'database') {
            return null;
        }

        $user = Auth::user();

        if (! $user instanceof Model) {
            return null;
        }

        if (! in_array(HasUiPreferences::class, class_uses_recursive($user), true)) {
            return null;
        }

        return $user;
    }

    private static function sessionKey(): string
    {
        return (string) config('ui-switcher.session_key', 'ui-switcher');
    }

    /**
     * Coerce a stored or submitted value into something the panel can safely use.
     */
    private static function sanitize(string $key, mixed $value): mixed
    {
        return match ($key) {
            self::COLOR => self::matchColor($value) ?? self::default(self::COLOR),
            self::FONT => self::firstOf(self::fonts(), $value, self::default(self::FONT)),
            self::LAYOUT => self::firstOf(self::layouts(), $value, self::default(self::LAYOUT)),
            self::DENSITY => self::firstOf(array_keys(self::densities()), $value, self::default(self::DENSITY)),
            self::FONT_SIZE => is_numeric($value)
                ? self::clampFontSize((int) $value)
                : self::default(self::FONT_SIZE),
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

    private static function clampFontSize(int $size): int
    {
        ['min' => $min, 'max' => $max] = self::fontSizeRange();

        return max($min, min($max, $size));
    }

    private static function assertKnownKey(string $key): void
    {
        if (! in_array($key, self::keys(), true)) {
            throw new InvalidArgumentException("Unknown UI preference [{$key}].");
        }
    }
}
