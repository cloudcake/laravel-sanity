<?php

namespace Sanity;

use Illuminate\Support\ServiceProvider;

class SanityServiceProvider extends ServiceProvider
{
    /**
     * Boot up the Sanity package.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/Config/config.php' => config_path('sanity.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/Config/phpcs.xml' => base_path('phpcs.xml'),
        ], 'config');
    }
}
