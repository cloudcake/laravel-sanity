<?php

namespace Sanity;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;

class SanityEventServiceProvider extends EventServiceProvider
{
    /**
     * The subscriber classes to register.
     *
     * @var array
     */
    protected $subscribe = [
        \Sanity\Subscriber::class,
    ];
}
