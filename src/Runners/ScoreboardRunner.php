<?php

namespace Sanity\Runners;

class ScoreboardRunner extends RunnerForMiniGame
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
     * Label to display for the badge.
     *
     * @var string
     */
    protected $badgeColourPassing = '99cc00';

    /**
     * Map of points to allocate on tests.
     *
     * @var array
     */
    protected $pointsMap = [
        'Dusk' => [
            'passing' => 2,
            'fixed'   => 5,
            'failing' => -3,
            'broken'  => -10,
        ],
        'Unit' => [
            'passing' => 2,
            'fixed'   => 5,
            'failing' => -3,
            'broken'  => -10,
        ],
        'Style' => [
            'passing' => 2,
            'fixed'   => 10,
            'failing' => -20,
            'broken'  => -25,
        ],
    ];

    /**
     * Runner execution.
     *
     * @return void
     */
    protected function run() : void
    {
        $results = $this->getResults();
        $runners = $this->getRealRunners(array_keys($this->pointsMap));
        $pusher = $this->getPusherName();
        $rState = '';
        $pCount = 0;

        foreach ($runners as $runner) {
            if ($runner->wasJustFixed()) {
                $rState = 'fixed';
            } elseif ($runner->wasJustBroken()) {
                $rState = 'broken';
            } elseif ($runner->isCurrentlyPassing()) {
                $rState = 'passing';
            } elseif ($runner->isCurrentlyFailing()) {
                $rState = 'failing';
            }

            $pCount = $this->pointsMap[$runner->getName()][$rState];
            $results = $this->updateScore($pusher, $results, $pCount);
        }

        $results = $this->updateRules($results);
        $results = $this->sortPlayersByPoints($results);
        $results = $this->createLabelForBadge($results);

        $this->setResults($results);
    }

    /**
     * Allocate points to player and return result.
     *
     * @param string $pusher  The pusher name,
     * @param array  $results The existing results set.
     * @param int    $points  The number of points to allocate.
     *
     * @return array
     */
    private function updateScore($pusher, $results, $points)
    {
        if (!isset($results['players'][$pusher])) {
            $results['players'][$pusher] = $points;
        } else {
            if ($points < 0) {
                $results['players'][$pusher] -= $points;
            } else {
                $results['players'][$pusher] += $points;
            }
        }

        return $results;
    }

    /**
     * Sort players by points in descening order.
     *
     * @param array $results The existing result set.
     *
     * @return array
     */
    private function sortPlayersByPoints(array $results)
    {
        $results['players'] = collect($results['players'])->sortByDesc(function ($value, $key) {
            return $value;
        })->all();

        return $results;
    }

    /**
     * Create displayable label for runner badge.
     *
     * @param array $results The existing result set.
     *
     * @return array
     */
    private function createLabelForBadge(array $results)
    {
        $topPlayers = array_slice($results['players'], 0, 3);
        $topPlayersTemp = [];

        foreach ($topPlayers as $leader => $points) {
            $topPlayersTemp[] = $leader.' ('.number_format($points).')';
        }

        $results['badge'] = str_replace('-', '--', implode(', ', $topPlayersTemp));

        return $results;
    }

    /**
     * Add the point map allocation to the result.
     *
     * @param array $results The existing result set.
     *
     * @return array
     */
    private function updateRules(array $results)
    {
        $results['rules'] = $this->pointsMap;

        return $results;
    }

    /**
     * Get mini game players.
     *
     * @return array
     */
    public function getPlayers()
    {
        return $this->getResults()['players'] ?? [];
    }

    /**
     * Get mini game rules.
     *
     * @return array
     */
    public function getRules()
    {
        return $this->getResults()['rules'] ?? $this->pointsMap;
    }

    /**
     * Get badge status.
     *
     * @return string
     */
    public function getBadgeStatus()
    {
        $results = $this->getResults();

        return rawurlencode($results['badge'] ?? 'none');
    }
}
