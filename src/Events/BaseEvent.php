<?php

namespace Sanity\Events;

use Illuminate\Queue\SerializesModels;

class BaseEvent
{
    use SerializesModels;

    /**
     * Array of test result lines.
     *
     * @var array
     */
    public $logs;

    /**
     * Array containing information about latest commit and committer.
     *
     * @var array
     */
    public $committer;

    /**
     * Array containing information about the commit and committer that
     * fixed the last successful test.
     *
     * @var array
     */
    public $fixer;

    /**
     * Array containing information about the commit and committer that
     * originally broke the test.
     *
     * @var array
     */
    public $destroyer;

    /**
     * Boolean indicating whether prior test result was is
     * different to the current.
     *
     * @var bool
     */
    public $changed;

    /**
     * Create a new event instance.
     *
     * @param array $results
     * @param bool  $passing
     * @param array $deployment
     *
     * @return void
     */
    public function __construct(array $committer, array $fixer, array $destroyer, array $logs, bool $changed)
    {
        $this->committer = $committer;
        $this->fixer = $fixer;
        $this->destroyer = $destroyer;
        $this->logs = $logs;
        $this->changed = $changed;
    }
}
