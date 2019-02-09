<?php

namespace Sanity;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;

class SanityServiceProvider extends EventServiceProvider
{
    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        \Sanity\Subscriber::class,
    ];

    /**
     * Boot up the Sanity package.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        
        $this->publishes([
            __DIR__.'/Config/config.php' => config_path('sanity.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/Config/phpcs.xml' => base_path('phpcs.xml'),
        ], 'config');

        if ($this->app->runningInConsole()) {
            $this->commands([
                \Sanity\Commands\SanityMock::class,
            ]);
        }
    }
}
