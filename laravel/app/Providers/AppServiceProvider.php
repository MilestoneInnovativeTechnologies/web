<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Illuminate\Support\Facades\Schema::defaultStringLength(191);
        \Illuminate\Support\Facades\Validator::extend('AMCActiveCustomer',"\\App\\Rules\\AMCActiveCustomer@passes");
//
//			\DB::listen(function($Q){
//				\Log::info('Query',[$Q->sql,$Q->bindings,$Q->time]);
//			});
		}

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
