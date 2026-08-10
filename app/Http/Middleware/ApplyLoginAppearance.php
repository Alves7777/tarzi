<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\LoginAppearance;
use Closure;
use Filament\Facades\Filament;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Applies the global login appearance to the panel while nobody is signed in.
 *
 * Signed-in requests are left alone: those are reconfigured from the user's own
 * preferences by `App\Http\Middleware\ApplyUiPreferences`, which runs in the
 * panel's auth middleware.
 */
class ApplyLoginAppearance
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $panel = Filament::getCurrentPanel();

        if ($panel === null || $panel->auth()->check()) {
            return $next($request);
        }

        // Filament generates the full palette from this single hex value, which
        // is what colours the side panel and its illustration.
        FilamentColor::register([
            'primary' => LoginAppearance::color(),
        ]);

        return $next($request);
    }
}
