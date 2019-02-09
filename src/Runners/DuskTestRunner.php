<?php

namespace Sanity\Runners;

class DuskTestRunner extends Runner
{
    /**
     * Identifier for the runner.
     *
     * @var string
     */
    protected $name = 'Dusk';

    /**
     * Label to display for the badge.
     *
     * @var string
     */
    protected $badgeLabel = 'dusk';

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
        $phpunitPath = config('php-unit-bin', base_path('vendor/bin/phpunit'));
        $phpunitConf = config('php-unit-dusk-xml', base_path('phpunit.dusk.xml'));

        exec("php {$phpunitPath} -c {$phpunitConf}", $results, $code);

        $this->setResults($results);

        ($code == 0) ? $this->markAsPassed() : $this->markAsFailed();
    }
}
