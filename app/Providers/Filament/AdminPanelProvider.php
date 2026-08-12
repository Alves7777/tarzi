<?php

namespace App\Providers\Filament;

use AchyutN\FilamentLogViewer\FilamentLogViewer;
use App\Filament\Pages\Auth\AdminLogin;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Plugins\UiSwitcherPlugin;
use App\Filament\Resources\Activitylog\ActivitylogResource;
use App\Http\Middleware\ApplyLoginAppearance;
use App\Http\Middleware\RedirectUnauthorizedPanelAccess;
use App\Support\LoginAppearance;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Auth\Pages\Login;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Rmsramos\Activitylog\ActivitylogPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->login(AdminLogin::class)
            ->databaseNotifications()
            ->profile(EditProfile::class, isSimple: false)
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->resourceCreatePageRedirect('index')
            ->resourceEditPageRedirect('index')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([])
            ->navigationGroups([
                NavigationGroup::make(fn (): string => __('app.navigation.administration'))
                    ->collapsible(),
            ])
            ->sidebarWidth('16rem')
            ->sidebarCollapsibleOnDesktop()
            ->plugins([
                UiSwitcherPlugin::make()
                    ->withModeSwitcher(),
                FilamentShieldPlugin::make()
                    ->navigationGroup(__('app.navigation.administration'))
                    ->navigationSort(2),
                ActivitylogPlugin::make()
                    ->resource(ActivitylogResource::class)
                    ->navigationGroup(__('app.navigation.administration'))
                    ->navigationSort(4),
                $this->logViewerPlugin(),
            ])
            ->renderHook(
                PanelsRenderHook::SIMPLE_LAYOUT_END,
                fn (): ?View => LoginAppearance::isSplit()
                    ? view('filament.auth.login-aside')
                    : null,
                scopes: Login::class,
            )
            ->renderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn (): View => view('filament.auth.anunciante-link'),
                scopes: AdminLogin::class,
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): View => view('filament.actions.print-listener'),
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->middleware([
                ApplyLoginAppearance::class,
            ], isPersistent: true)
            ->authMiddleware([
                RedirectUnauthorizedPanelAccess::class,
            ]);
    }

    private function logViewerPlugin(): FilamentLogViewer
    {
        $plugin = FilamentLogViewer::make()
            ->navigationGroup(__('app.navigation.administration'))
            ->navigationSort(5)
            ->authorize(fn (): bool => Gate::allows('View:LogTable'));

        $plugin->registerNavigation = fn (): bool => $plugin->isAuthorized();

        return $plugin;
    }
}
