<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    private $composers = [
        'notification.notifications' => 'App\Http\Controllers\NotificationController',
        'log.comp_recentusage' => \App\Composer\RecentAppUsages::class,
    ];
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        foreach ($this->composers as $name => $class)
            View::composer($name,$class);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
