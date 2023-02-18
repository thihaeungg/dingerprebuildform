<?php

namespace Hmm\Dingerprebuildform;

use Illuminate\Support\ServiceProvider;

class DingerServiceProvider extends ServiceProvider
{
  public function register()
  {
    //
  }

  public function boot()
  {
    if ($this->app->runningInConsole()) {

      $this->publishes([
        __DIR__.'/../config/config.php' => config_path('dinger.php'),
      ], 'config');
  
    }
  }
}