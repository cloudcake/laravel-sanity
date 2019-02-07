<?php

namespace Sanity\Events;

use Illuminate\Queue\SerializesModels;

class StandardsFinished
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
     * Create a new event instance.
     *
     * @param array $results
     * @param bool  $passing
     *
     * @return void
     */
    public function __construct(array $results, bool $passing)
    {
        $this->results = $results;
        $this->passing = $passing;
    }
}
