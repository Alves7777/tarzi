<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Enabled
    |--------------------------------------------------------------------------
    |
    | Turns the whole UI switcher off without having to remove the plugin from
    | the panel. When disabled, the trigger is not rendered and no preference
    | is applied to the panel.
    |
    */

    'enabled' => env('UI_SWITCHER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Storage Driver
    |--------------------------------------------------------------------------
    |
    | "session"  preferences live in the session and are lost on logout.
    | "database" preferences are stored on the authenticated user, so they
    |            follow the user across devices and sessions.
    |
    | The database driver requires the `add_ui_preferences_to_users_table`
    | migration and the `App\Models\Concerns\HasUiPreferences` trait on the
    | authenticatable model. It silently falls back to the session when the
    | user is a guest or the trait is missing.
    |
    */

    'driver' => env('UI_SWITCHER_DRIVER', 'session'),

    'session_key' => 'ui-switcher',

    'database_column' => 'ui_preferences',

    /*
    |--------------------------------------------------------------------------
    | Trigger Icon
    |--------------------------------------------------------------------------
    */

    'icon' => 'heroicon-o-cog-6-tooth',

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    |
    | Used until the user picks something else, and restored by the reset
    | button. Every value must exist in the corresponding list below.
    |
    */

    'defaults' => [
        'color' => '#6366f1',
        'font' => 'Inter',
        'font_size' => 16,
        'layout' => 'sidebar',
        'density' => 'default',
    ],

    /*
    |--------------------------------------------------------------------------
    | Colors
    |--------------------------------------------------------------------------
    |
    | Swatches offered in the panel, keyed by the label shown to the user.
    | Filament generates the full primary palette from each hex value. Any
    | value outside this list is rejected, so a tampered request can never
    | inject arbitrary CSS.
    |
    */

    'colors' => [
        'indigo' => '#6366f1',
        'blue' => '#3b82f6',
        'sky' => '#0ea5e9',
        'teal' => '#14b8a6',
        'emerald' => '#10b981',
        'lime' => '#84cc16',
        'amber' => '#f59e0b',
        'orange' => '#f97316',
        'red' => '#ef4444',
        'rose' => '#f43f5e',
        'pink' => '#ec4899',
        'violet' => '#8b5cf6',
    ],

    /*
    |--------------------------------------------------------------------------
    | Fonts
    |--------------------------------------------------------------------------
    |
    | Families served by Filament's default font provider (bunny.net, a
    | privacy friendly Google Fonts mirror). Names must match the provider.
    |
    */

    'fonts' => [
        'Inter',
        'Poppins',
        'Public Sans',
        'DM Sans',
        'Nunito Sans',
        'Roboto',
    ],

    /*
    |--------------------------------------------------------------------------
    | Font Size
    |--------------------------------------------------------------------------
    |
    | Root font size in pixels. Filament sizes everything in `rem`, so this
    | scales the entire panel. Values are clamped to this range.
    |
    */

    'font_size' => [
        'min' => 12,
        'max' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Densities
    |--------------------------------------------------------------------------
    |
    | Each density overrides Tailwind's `--spacing` base unit, which every
    | padding, margin and gap in Filament is derived from.
    |
    */

    'densities' => [
        'compact' => '0.2rem',
        'default' => '0.25rem',
        'comfortable' => '0.3rem',
    ],

    /*
    |--------------------------------------------------------------------------
    | Layouts
    |--------------------------------------------------------------------------
    |
    | Remove entries to hide them from the panel:
    |
    | "sidebar"           full sidebar with icons and labels
    | "sidebar-collapsed" sidebar collapsed to icons only
    | "sidebar-no-topbar" sidebar with the topbar hidden
    | "topbar"            top navigation, no sidebar
    |
    */

    'layouts' => [
        'sidebar',
        'sidebar-collapsed',
        'sidebar-no-topbar',
        'topbar',
    ],

];
