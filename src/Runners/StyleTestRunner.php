<?php

namespace Sanity\Runners;

class StyleTestRunner extends Runner
{
    /**
     * Identifier for the runner.
     *
     * @var string
     */
    protected $name = 'Style';

    /**
     * Label to display for the badge.
     *
     * @var string
     */
    protected $badgeLabel = 'style';

    /**
     * Indicate whether or not this runner should fire events.
     *
     * @var boolean
     */
    protected $shouldFireEvents = true;

    /**
     * Runner execution.
     *
     * @return void
     */
    protected function run() : void
    {
        $phpcsPath = config('php-cs-bin', base_path('vendor/bin/phpcs'));

        $results = json_decode(exec("php {$phpcsPath} --report=json"), true);

        $this->setResults($results);

        ($results['totals']['errors'] == 0) ? $this->passed() : $this->failed();
    }
}
