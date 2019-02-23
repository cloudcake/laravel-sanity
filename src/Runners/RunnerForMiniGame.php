<?php

namespace Sanity\Runners;

use Sanity\Factory;

class RunnerForMiniGame extends Runner
{
    /**
     * Indicate whether this runner collects stats.
     *
     * @var bool
     */
    protected $collectsStats = true;

    /**
     * Get runners that do not collect stats.
     *
     * @param array|false $runnerNames
     *
     * @return array
     */
    public function getRealRunners($runnerNames = false)
    {
        $runners = Factory::$runners;

        if ($runnerNames) {
            $runners = collect($runners)->filter(function ($runner, $key) use ($runnerNames) {
                return in_array($runner->getName(), $runnerNames, true);
            })->all();
        }

        return $runners;
    }

    /**
     * Get the name of the pusher.
     *
     * @return string|null
     */
    public function getPusherName()
    {
        return $this->getCommit()->author->name ?? null;
    }
}
