<?php

namespace Sanity\Runners;

class PusherPointsRunner extends Runner
{
    /**
     * Identifier for the runner.
     *
     * @var string
     */
    protected $name = 'Pushers';

    /**
     * Label to display for the badge.
     *
     * @var string
     */
    protected $badgeLabel = 'Top Pushers';

    /**
     * Indicate whether or not this runner should fire events.
     *
     * @var boolean
     */
    protected $shouldFireEvents = true;

    /**
     * Label to display for the badge.
     *
     * @var string
     */
    protected $badgeColourPassing = '99cc00';

    /**
     * Indicate whether this runner collects stats.
     *
     * @var boolean
     */
    protected $collectsStats = true;

    /**
     * Map of points to allocate
     *
     * @var integer
     */
    protected $points = 1;

    /**
     * Runner execution.
     *
     * @return void
     */
    protected function run() : void
    {
        $results = $this->getResults();

        $runners = collect(\Sanity\Factory::$runners)->filter(function ($runner, $key) {
            return $runner->collectsStats() == false;
        })->all();

        $commit = $this->getCommit();

        $player = $commit['commit_author'];
        $points = $results['players'][$player] ?? 0;

        $results['players'][$player] = ($points += $this->points);

        $results['status'] = 'none';

        $pushers = collect($results['players'])->sortByDesc(function ($value, $key) {
            return $value;
        })->all();

        $pushers = array_slice($results['players'], 0, 3);
        $pushersTmp = [];

        foreach ($pushers as $saviour => $points) {
            $pushersTmp[] = $saviour.' ('.number_format($points).')';
        }

        $results['status'] = str_replace('-', '--', implode(', ', $pushersTmp));

        $this->setResults($results);
        $this->markAsPassed();
    }

    /**
     * Get badge status.
     *
     * @return string
     */
    public function getBadgeStatus()
    {
        return rawurlencode($this->getResults()['status'] ?? 'none');
    }
}
