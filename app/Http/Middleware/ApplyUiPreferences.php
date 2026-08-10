<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\UiPreferences;
use Closure;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the stored UI preferences to the current panel.
 *
 * The panel boots in the `panel:{id}` middleware, which runs before the session
 * is started, so preferences cannot be resolved from the panel configuration
 * itself. This middleware runs after `StartSession` and reconfigures the panel
 * before anything is rendered.
 */
class ApplyUiPreferences
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $panel = Filament::getCurrentPanel();

        if ($panel === null || ! UiPreferences::isEnabled()) {
            return $next($request);
        }

        $preferences = UiPreferences::all();

        // Filament generates the full palette from this single hex value. It is
        // registered here rather than through `$panel->colors()` because the
        // panel has already booted by the time this middleware runs.
        FilamentColor::register([
            'primary' => $preferences[UiPreferences::COLOR],
        ]);

        $panel->font($preferences[UiPreferences::FONT]);

        // The sidebar is collapsible in every layout; "sidebar-collapsed" only
        // differs in starting that way, which the switcher sets client side
        // because the open state lives in local storage.
        match ($preferences[UiPreferences::LAYOUT]) {
            'topbar' => $panel->topNavigation(),
            'sidebar-collapsed' => $panel->sidebarCollapsibleOnDesktop(),
            'sidebar-no-topbar' => $panel->topbar(false),
            default => $panel,
        };

        return $next($request);
    }
}
