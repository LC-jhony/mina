<?php

namespace App\Providers;

use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // FilamentView::registerRenderHook(
        //     PanelsRenderHook::SIDEBAR_LOGO_BEFORE,
        //     fn (): View => view('components.logo')
        // );
    }
}
