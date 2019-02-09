<?php

namespace Sanity\Runners;

class SaviourPointsRunner extends Runner
{
    /**
     * Identifier for the runner.
     *
     * @var string
     */
    protected $name = 'Saviours';

    /**
     * Label to display for the badge.
     *
     * @var string
     */
    protected $badgeLabel = 'Top Saviours';

    /**
     * Indicate whether or not this runner should fire events.
     *
     * @var bool
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
     * @var bool
     */
    protected $collectsStats = true;

    /**
     * Map of points to allocate.
     *
     * @var int
     */
    protected $points = 10;

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

        foreach ($runners as $runner) {
            if ($runner->wasSaved()) {
                $results['players'][$player] = ($points += $this->points);
            }
        }

        $results['status'] = 'none';

        if (count($results['players'])) {
            $saviours = collect($results['players'])->sortByDesc(function ($value, $key) {
                return $value;
            })->all();

            $saviours = array_slice($results['players'], 0, 3);
            $savioursTmp = [];

            foreach ($saviours as $saviour => $points) {
                $savioursTmp[] = $saviour.' ('.number_format($points).')';
            }

            $results['status'] = str_replace('-', '--', implode(', ', $savioursTmp));
        }

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
