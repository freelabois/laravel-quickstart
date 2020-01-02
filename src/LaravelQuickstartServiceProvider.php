<?php

namespace Freelabois\LaravelQuickstart;

use Illuminate\Support\ServiceProvider;

class LaravelQuickstartServiceProvider extends ServiceProvider
{
   /**
    * Indicates if loading of the provider is deferred.
    *
    * @var bool
    */
   protected $defer = false;

   /**
    * Perform post-registration booting of services.
    *
    * @return void
    */
   public function boot()
   {
   }

   /**
    * Register any package services.
    *
    * @return void
    */
   public function register()
   {
      $this->registerLaravelQuickstart();

   }

   private function registerLaravelQuickstart()
   {
      $this->app->bind('laravel-quickstart',function($app){
         return new LaravelQuickstart($app);
      });
   }
}
