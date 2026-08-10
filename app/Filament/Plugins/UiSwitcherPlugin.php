<?php

declare(strict_types=1);

namespace App\Filament\Plugins;

use App\Http\Middleware\ApplyUiPreferences;
use App\Support\UiPreferences;
use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;

/**
 * Adds the UI switcher trigger to a panel and applies the stored preferences.
 *
 * ```php
 * $panel->plugin(
 *     UiSwitcherPlugin::make()
 *         ->iconRenderHook(PanelsRenderHook::TOPBAR_END)
 *         ->withModeSwitcher(),
 * );
 * ```
 */
class UiSwitcherPlugin implements Plugin
{
    protected string $iconRenderHook = PanelsRenderHook::USER_MENU_BEFORE;

    protected bool $hasModeSwitcher = false;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'ui-switcher';
    }

    /**
     * Move the trigger to any panel render hook.
     */
    public function iconRenderHook(string $hook): static
    {
        $this->iconRenderHook = $hook;

        return $this;
    }

    /**
     * Show Filament's light/dark/system switcher inside the slide-over.
     */
    public function withModeSwitcher(bool $condition = true): static
    {
        $this->hasModeSwitcher = $condition;

        return $this;
    }

    public function register(Panel $panel): void
    {
        // Runs after `StartSession`, unlike the panel boot, and is persistent so
        // Livewire requests are configured the same way as full page loads.
        $panel->authMiddleware([
            ApplyUiPreferences::class,
        ], isPersistent: true);

        $panel->renderHook(
            $this->iconRenderHook,
            fn (): ?View => UiPreferences::isEnabled()
                ? view('filament.ui-switcher.trigger', ['hasModeSwitcher' => $this->hasModeSwitcher])
                : null,
        );

        // The font size and density are plain CSS, so they do not need the
        // panel to be reconfigured, only a couple of custom properties.
        $panel->renderHook(
            PanelsRenderHook::HEAD_END,
            fn (): ?View => UiPreferences::isEnabled()
                ? view('filament.ui-switcher.styles')
                : null,
        );
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
