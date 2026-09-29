<?php

namespace App\Providers\Filament;

use App\Filament\Pages\LaporanPresensiPage;
use App\Filament\Pages\PengaturanSekolahPage;
use App\Filament\Pages\RincianPresensiPage;
use App\Filament\Widgets\AdminExecutiveOverviewWidget;
use App\Filament\Widgets\AnomaliPresensiWidget;
use App\Filament\Widgets\GuruTerbaruPresensiWidget;
use App\Filament\Widgets\LeaderboardDisiplinWidget;
use App\Filament\Widgets\PeringatanKeterlambatanWidget;
use App\Filament\Widgets\PresensiStatistikChartWidget;
use App\Filament\Widgets\PresensiTrenChartWidget;
use App\Http\Middleware\RedirectToUnifiedLogin;
use App\Models\PengaturanSekolah;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
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
use TomatoPHP\FilamentUsers\FilamentUsersPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('sekolahku/panel')
            // ->spa()
            ->brandName(fn () => PengaturanSekolah::getSetting()->nama_sekolah ?: 'Absensi Guru')
            ->brandLogo(fn () => view('filament.components.brand-logo', ['panel' => 'admin', 'title' => 'Absensi Guru']))
            ->brandLogoHeight('2.6rem')
            ->favicon(fn () => PengaturanSekolah::getSetting()->logo_url ?: asset('icons/icon.svg'))
            ->maxContentWidth(Width::Full)
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups([
                'Presensi & Kehadiran',
                'Data Master',
                'Pengaturan & Sistem',
                'Filament Shield',
            ])
            ->login(null)
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->plugins([
                FilamentMobilePresetPlugin::make()    // restores "Create & create another"
                    ->bottomNav(MobileBottomNav::make()->fromNavigation(5))  // or ->bottomNav(false)
                    ->thumbAlignment(false)
                    ->slideOverModals(false)
                    ->createAnother()          // restores "Create & create another"
                // ->moreButtonLabel('Menu')
                ,
                FilamentShieldPlugin::make(),
                FilamentUsersPlugin::make()
                    ->useUserResource(false)
                    ->useTeamsResource(false)
                    ->useAvatar(true),
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        shouldRegisterNavigation: false,
                        hasAvatars: true,
                        slug: 'my-profile'
                    )
                    ->enableTwoFactorAuthentication(),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
                LaporanPresensiPage::class,
                RincianPresensiPage::class,
                PengaturanSekolahPage::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AdminExecutiveOverviewWidget::class,
                AnomaliPresensiWidget::class,
                PresensiTrenChartWidget::class,
                PresensiStatistikChartWidget::class,
                GuruTerbaruPresensiWidget::class,
                LeaderboardDisiplinWidget::class,
                PeringatanKeterlambatanWidget::class,
            ])
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
