<?php

namespace TinkLabs\Bank;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class ServiceProvider extends BaseServiceProvider
{    
    public function boot()
    {
        $this->app->group(['namespace' => __NAMESPACE__ . '\Controllers'], function ($app) {
            require __DIR__ . '/routes/web.php';
        });
    }
}
