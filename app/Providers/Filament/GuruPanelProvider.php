<?php

namespace App\Providers\Filament;

use App\Filament\Guru\Pages\Dashboard;
use App\Http\Middleware\RedirectToUnifiedLogin;
use App\Models\PengaturanSekolah;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Width;
use Filament\View\PanelsRenderHook;
use Hammadzafar05\FilamentMobilePreset\FilamentMobilePresetPlugin;
use Hammadzafar05\MobileBottomNav\MobileBottomNav;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Blade;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;

class GuruPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('guru')
            ->path('guru')
            ->brandName(fn () => PengaturanSekolah::getSetting()->nama_sekolah ?: 'Portal Guru & Presensi')
            ->brandLogo(fn () => view('filament.components.brand-logo', ['panel' => 'guru', 'title' => 'Portal Guru & Presensi']))
            ->brandLogoHeight('2.6rem')
            ->favicon(fn () => PengaturanSekolah::getSetting()->logo_url ?: asset('icons/icon.svg'))
            ->maxContentWidth(Width::Full)
            ->sidebarCollapsibleOnDesktop()
            // ->spa()
            ->login(null)
            ->colors([
                'primary' => Color::Teal,
            ])
            ->plugins([
                FilamentMobilePresetPlugin::make()    // restores "Create & create another"
                    ->bottomNav(MobileBottomNav::make()->fromNavigation(5))  // or ->bottomNav(false)
                    ->thumbAlignment(false)
                    ->slideOverModals(false)
                    ->createAnother()          // restores "Create & create another"
                // ->moreButtonLabel('Menu')
                ,
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        shouldRegisterNavigation: false,
                        hasAvatars: true,
                        slug: 'my-profile'
                    )
                    ->enableTwoFactorAuthentication(),
            ])
            ->navigationGroups([
                'Menu Utama',
                'Layanan Guru',
            ])
            ->discoverResources(in: app_path('Filament/Guru/Resources'), for: 'App\Filament\Guru\Resources')
            ->discoverPages(in: app_path('Filament/Guru/Pages'), for: 'App\Filament\Guru\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Guru/Widgets'), for: 'App\Filament\Guru\Widgets')
            ->widgets([])
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
            ->authMiddleware([
                RedirectToUnifiedLogin::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_END,
                fn (): string => Blade::render('@include("filament.pwa-head")')
            )
            ->renderHook(
                PanelsRenderHook::BODY_END,
                fn (): string => Blade::render('@include("filament.pwa-body")')
            );
    }
}
