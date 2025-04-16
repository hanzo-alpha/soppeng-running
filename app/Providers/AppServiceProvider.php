<?php

declare(strict_types=1);

namespace App\Providers;

use App\Filament\App\Pages\Pendaftaran;
use App\Policies\ActivityPolicy;
use BezhanSalleh\FilamentShield\FilamentShield;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Vite;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Spatie\Activitylog\Models\Activity;

class AppServiceProvider extends ServiceProvider
{
    protected array $policies = [
        Activity::class => ActivityPolicy::class,
    ];

    public function register(): void {}

    public function boot(): void
    {
        $this->configurePolicies();

        $this->configureDB();

        $this->configureModels();

        $this->configureFilament();

        $this->configureVite();

        $this->configureCommands();

        $this->registerFilamentHook();
    }

    private function configurePolicies(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    private function configureDB(): void
    {
        DB::prohibitDestructiveCommands($this->app->environment('production'));
    }

    private function configureModels(): void
    {
        Model::shouldBeStrict($this->app->isProduction());

        Model::unguard();
    }

    private function configureFilament(): void
    {
        FilamentShield::prohibitDestructiveCommands($this->app->isProduction());
    }

    private function configureCommands(): void
    {
        DB::prohibitDestructiveCommands($this->app->isProduction());
    }

    private function registerFilamentHook(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::BODY_END,
            fn(array $scopes): View => view('payment-js', ['scopes' => $scopes]),
            scopes: [
                Pendaftaran::class,
            ],
        );
    }

    private function configureVite(): void
    {
        \Illuminate\Support\Facades\Vite::useWaterfallPrefetching(concurrency: 10);
    }
}
