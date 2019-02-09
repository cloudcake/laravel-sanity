<?php

namespace Sanity\Runners;

class ScoreboardRunner extends Runner
{
    /**
     * Identifier for the runner.
     *
     * @var string
     */
    protected $name = 'Scoreboard';

    /**
     * Label to display for the badge.
     *
     * @var string
     */
    protected $badgeLabel = 'Scoreboard';

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
     * Runner execution.
     *
     * @return void
     */
    protected function run() : void
    {
        $results = $this->getResults();

        $runnersWanted = ['Saviours', 'Butchers', 'Pushers'];

        $runners = collect(\Sanity\Factory::$runners)->filter(function ($runner, $key) use ($runnersWanted) {
            return in_array($runner->getName(), $runnersWanted, true);
        })->all();

        $player = $this->getCommit()['commit_author'];
        $playerPoints = $this->getResults()['players'][$player] ?? 0;

        foreach ($runners as $runner) {
            $runnerPoints = $runner->getResults()['players'][$player] ?? 0;
            $playerPoints = ($playerPoints += $runnerPoints);
        }

        $results['players'][$player] = $playerPoints;
        $results['players'] = collect($results['players'])->sortByDesc(function ($value, $key) {
            return $value;
        })->all();

        $this->setResults($results);
    }

    /**
     * Get badge status.
     *
     * @return string
     */
    public function getBadgeStatus()
    {
        return 'Incompatible';
    }
}
