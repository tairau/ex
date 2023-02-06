<?php

declare(strict_types = 1);

namespace App\Services\Database;

use Illuminate\Contracts\Container\Container;
use Illuminate\Support\ServiceProvider;

class DatabaseServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind(Transactional::class, function (Container $app) {
            /** @var \Illuminate\Database\DatabaseManager $db */
            $db = $app->make('db');

            /** @var \Illuminate\Config\Repository $config */
            $config = $app->make('config');

            return new Transactional($db->connection($config->get('database.default')));
        });
    }
}
