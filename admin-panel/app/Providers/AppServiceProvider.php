<?php

namespace App\Providers;

use App\Events\StudentSuccessApproved;
use App\Listeners\GenerateAutoCertificate;
use App\Listeners\NotifyAdminForManualCertificate;
use App\Http\View\Composers\HeaderComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enregistrer le View Composer pour le header
        View::composer('layouts.header', HeaderComposer::class);

        // Register event listeners for StudentSuccessApproved
        Event::listen(
            StudentSuccessApproved::class,
            [GenerateAutoCertificate::class, 'handle']
        );
        Event::listen(
            StudentSuccessApproved::class,
            [NotifyAdminForManualCertificate::class, 'handle']
        );
    }
}
