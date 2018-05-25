<?php

namespace TinkLabs\Bank;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

class ServiceProvider extends BaseServiceProvider
{
    protected $providerName = 'bank';

    public function boot()
    {
        $this->app->group(['namespace' => __NAMESPACE__ . '\Controllers'], function ($app) {
            require __DIR__ . '/routes.php';
        });
    }

    public function register()
    {
        if ($this->app instanceof LumenApplication) {
            $this->app->configure($this->providerName);
        }

        // Module config 模块配置
        $this->mergeConfigFrom(
            sprintf(__DIR__ . '/config/%s.php', $this->providerName), $this->providerName
        );        
    }
}
