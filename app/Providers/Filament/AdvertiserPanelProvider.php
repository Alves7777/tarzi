<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\AdvertiserLogin;
use App\Http\Middleware\RedirectUnauthorizedPanelAccess;
use App\Filament\Advertiser\Resources\Advertisements\AdvertisementResource;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdvertiserPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('advertiser')
            ->path('anunciante')
            ->login(AdvertiserLogin::class)
            ->homeUrl(fn (): string => AdvertisementResource::getUrl('index', panel: 'advertiser'))
            ->brandName(__('signage.advertiser_panel.title'))
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->resources([
                AdvertisementResource::class,
            ])
            ->pages([])
            ->widgets([])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                RedirectUnauthorizedPanelAccess::class,
            ]);
    }
}
