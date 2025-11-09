<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Zalo\Zalo;

class ZaloServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Zalo::class, function (Application $app): Zalo {
            $config = [
                'app_id' => config('zalo.app_id'),
                'app_secret' => config('zalo.app_secret'),
            ];

            return new Zalo($config);
        });
    }

    public function boot(): void {}
}
