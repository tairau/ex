<?php

declare(strict_types = 1);

namespace App\Services\ApiLayer;

use App\Services\ApiLayer\Exchange\ExchangeClient;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\ServiceProvider;

class ExchangeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind('apilayer.http', function (Container $app) {
            /** @var \Illuminate\Config\Repository $config */
            $config = $app['config'];
            $serviceConfig = $config->get('services.apilayer');

            return Http::acceptJson()
                ->asJson()
                ->baseUrl($serviceConfig['url'] ?? '')
                ->withHeaders([
                    'apikey' => $serviceConfig['key'] ?? '',
                ]);
        });

        $this->app->when(ExchangeClient::class)
            ->needs(PendingRequest::class)
            ->give(function (Container $app) {
                return $app->make('apilayer.http');
            });
    }
}
