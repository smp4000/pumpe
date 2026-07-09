<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use App\Filament\App\Pages\Tenancy\RegisterOrganization;
use App\Http\Middleware\ApplyTenantContext;
use App\Models\Organization;
use App\Modules\ModuleManager;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * Mandanten-Panel (/app): Arbeitsoberfläche der Tankstellenbetreiber.
 * Tenant ist die Organization; der Stationskontext wird innerhalb des
 * Panels gewählt.
 */
class AppPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('app')
            ->path('app')
            ->brandName('Pumpe')
            ->login()
            ->registration()
            ->passwordReset()
            ->colors([
                'primary' => Color::Emerald,
            ])
            ->tenant(Organization::class, slugAttribute: 'slug')
            // Filament-Plugins aller Module; Sichtbarkeit pro Tenant regeln
            // BelongsToModule-Trait und Policies (Lizenzprüfung)
            ->plugins(app(ModuleManager::class)->filamentPlugins())
            ->tenantRegistration(RegisterOrganization::class)
            ->tenantMiddleware([
                ApplyTenantContext::class,
            ], isPersistent: true)
            ->discoverResources(in: app_path('Filament/App/Resources'), for: 'App\Filament\App\Resources')
            ->discoverPages(in: app_path('Filament/App/Pages'), for: 'App\Filament\App\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/App/Widgets'), for: 'App\Filament\App\Widgets')
            ->widgets([
                AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
