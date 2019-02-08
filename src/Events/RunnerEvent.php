<?php

namespace Sanity\Events;

use Illuminate\Queue\SerializesModels;

class RunnerEvent
{
    use SerializesModels;

    /**
     * Instance of runner.
     *
     * @var \Sanity\Runners\Runner
     */
    public $runner;

    /**
     * Create a new event instance.
     *
     * @param \Sanity\Runners\Runner $runner The runner that completed.
     *
     * @return void
     */
    public function __construct(\Sanity\Runners\Runner $runner)
    {
        $this->runner = $runner;
    }
}
