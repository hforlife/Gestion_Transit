<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Swis\Filament\Backgrounds\FilamentBackgroundsPlugin;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Pages\Dashboard;
use BezhanSalleh\FilamentShield\Resources\Roles\RoleResource;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Swis\Filament\Backgrounds\ImageProviders\MyImages;
// Pages
use App\Filament\Pages\RapportClients;
use App\Filament\Pages\RapportColis;
use App\Filament\Pages\RapportBlocages;
// Resources
use App\Filament\Resources\Colis\ColisResource;
use App\Filament\Resources\DossierTransits\DossierTransitResource;
use App\Filament\Resources\Documents\DocumentResource;
use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Users\UserResource;
use App\Filament\Resources\Ports\PortResource;
use App\Filament\Resources\TypeDossiers\TypeDossierResource;
use App\Filament\Resources\TypeColis\TypeColisResource;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->navigation(function(NavigationBuilder $builder) {
                return $builder->groups([
                    NavigationGroup::make('Tableau de bord')
                        ->items([
                            ...Dashboard::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Gestion de transit')
                        ->items([
                            ...ColisResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Archives')
                        ->items([
                            ...DossierTransitResource::getNavigationItems(),
                            ...DocumentResource::getNavigationItems(),
                        ]),
                    NavigationGroup::make('Gestion des Utilisateurs')
                        ->items([
                            ...ClientResource::getNavigationItems(),
                            ...UserResource::getNavigationItems(),
                        ]),
                   NavigationGroup::make('Rapports')
                    ->items([
                        ...RapportColis::getNavigationItems(),
                        ...RapportClients::getNavigationItems(),
                        ...RapportBlocages::getNavigationItems(),

                    ]),
                   NavigationGroup::make('Paramètres du Système')
                    ->items([
                        ...RoleResource::getNavigationItems(),
                        ...TypeColisResource::getNavigationItems(),
                        ...TypeDossierResource::getNavigationItems(),
                        ...PortResource::getNavigationItems(),
                    ])
                ]);
            })
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                FilamentInfoWidget::class,
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
            ->plugins([
                FilamentShieldPlugin::make(),
                FilamentBackgroundsPlugin::make()
                        ->imageProvider(
                MyImages::make()
                            ->directory('images/backgrounds/')
                ),
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            // ->spa()
            ->favicon(asset('images/favicon.png'))
            ->brandLogo(asset('images/logo/logo_white.png'))
            ->darkModeBrandLogo(asset('images/logo/logo_black.png'))
            ->brandLogoHeight('70px')
            ->colors([
                'primary' => Color::Amber,
            ]);
    }
}
