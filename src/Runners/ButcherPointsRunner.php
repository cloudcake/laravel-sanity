<?php

namespace Sanity\Runners;

class ButcherPointsRunner extends Runner
{
    /**
     * Identifier for the runner.
     *
     * @var string
     */
    protected $name = 'Butchers';

    /**
     * Label to display for the badge.
     *
     * @var string
     */
    protected $badgeLabel = 'Top Butchers';

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
    protected $badgeColourPassing = 'c53232';

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
    protected $points = -20;

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
            if ($runner->wasButchered()) {
                $results['players'][$player] = ($points += $this->points);
            }
        }

        $results['status'] = 'none';

        if (count($results['players'])) {
            $butchers = collect($results['players'])->sortByDesc(function ($value, $key) {
                return $value;
            })->all();

            $butchers = array_slice($results['players'], 0, 3);
            $butchersTmp = [];

            foreach ($butchers as $butcher => $points) {
                $butchersTmp[] = $butcher.' ('.number_format($points).')';
            }

            $results['status'] = str_replace('-', '--', implode(', ', $butchersTmp));
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
