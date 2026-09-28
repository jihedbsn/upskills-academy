<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use App\Models\Category;
use App\Models\Information;
use App\Services\CurrencyService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register CurrencyService as singleton
        $this->app->singleton(CurrencyService::class, function ($app) {
            return new CurrencyService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register currency formatting Blade directive
        // Usage: @price($amount) or @price($amount, 'EUR')
        Blade::directive('price', function ($expression) {
            return "<?php echo app(\App\Services\CurrencyService::class)->format($expression); ?>";
        });

        // Register directive for raw converted amount (without formatting)
        // Usage: @priceRaw($amount)
        Blade::directive('priceRaw', function ($expression) {
            return "<?php echo app(\App\Services\CurrencyService::class)->convert($expression); ?>";
        });

        // Auto-detect currency on first visit
        if (!app()->runningInConsole()) {
            app(CurrencyService::class)->autoDetectCurrency();
        }

        // Share categories with header view
        View::composer('layouts.header', function ($view) {
            $categories = Category::get();
            $view->with('categories', $categories);
        });

        // Share information data with footer view
        View::composer('layouts.footer', function ($view) {
            $information = Information::first();
            $view->with('information', $information);
        });

        // Share pending evaluations with dashboard layout
        View::composer('layouts.dashboard', function ($view) {
            $evaluationService = new \App\Services\EvaluationService();
            $pendingEvaluations = $evaluationService->getPendingEvaluations();
            $view->with('pendingEvaluations', $pendingEvaluations);
            $view->with('pendingEvaluationsCount', $pendingEvaluations->count());
        });

        // Share pending evaluations with student-dashboard layout
        View::composer('student-dashboard.layouts.app', function ($view) {
            $evaluationService = new \App\Services\EvaluationService();
            $pendingEvaluations = $evaluationService->getPendingEvaluations();
            $view->with('pendingEvaluations', $pendingEvaluations);
            $view->with('pendingEvaluationsCount', $pendingEvaluations->count());
        });

        // Share unread notifications with student-dashboard header
        View::composer('student-dashboard.layouts.header', \App\View\Composers\NotificationComposer::class);
    }
}
