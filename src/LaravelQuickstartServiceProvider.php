<?php

namespace Freelabois\LaravelQuickstart;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use Freelabois\LaravelQuickstart\Models\AgendamentoVeiculoUso;
use Freelabois\LaravelQuickstart\Models\ApontamentoVeiculoGasto;
use Freelabois\LaravelQuickstart\Models\ApontamentoVeiculoGastoAnexo;
use Freelabois\LaravelQuickstart\Models\ApontamentoVeiculoGastoItem;
use Freelabois\LaravelQuickstart\Models\ApontamentoVeiculoUso;
use Freelabois\LaravelQuickstart\Models\ProdutoFrota;
use Freelabois\LaravelQuickstart\Models\Veiculo;
use Freelabois\LaravelQuickstart\Observers\VeiculoObserver;

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
      $this->registerGef();

   }

   private function registerGef()
   {
      $this->app->bind('laravel-quickstart',function($app){
         return new LaravelQuickstart($app);
      });
   }
}
