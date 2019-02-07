<?php

namespace Sanity\Events;

use Illuminate\Queue\SerializesModels;

class SanityBaseEvent
{
    use SerializesModels;

    /**
     * Array of test result lines.
     *
     * @var array
     */
    public $results;

    /**
     * Boolean indicating whether the test was successful.
     *
     * @var bool
     */
    public $passing;

    /**
     * Array containing forge payload.
     *
     * @var array
     */
    public $deployment;

    /**
     * Create a new event instance.
     *
     * @param array $results
     * @param bool  $passing
     * @param array $deployment
     *
     * @return void
     */
    public function __construct(array $results, bool $passing, array $deployment)
    {
        $this->results = $results;
        $this->passing = $passing;
        $this->deployment = $deployment;
    }
}
