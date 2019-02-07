<?php

namespace Sanity;

use Illuminate\Support\Facades\Cache;
use Sanity\Events\DuskSucceeded;
use Sanity\Events\DuskFailed;
use Sanity\Events\StyleSucceeded;
use Sanity\Events\StyleFailed;
use Sanity\Events\UnitSucceeded;
use Sanity\Events\UnitFailed;

class Factory
{
    /**
     * Cache instance.
     *
     * @var \lluminate\Cache\CacheManager
     */
    private $cache;

    /**
     * Deployment payload from Forge.
     *
     * @var array
     */
    private $deployment;

    /**
     * Create new instance of Badges.
     *
     * @return void
     */
    public function __construct()
    {
        $this->cache = Cache::store(config('sanity.cache'), 'file');
    }

    /**
     * Get package routes.
     *
     * @return mixed
     */
    public static function routes()
    {
        require __DIR__.'/Routes/routes.php';
    }

    /**
     * Run all configured tasks.
     *
     * @return void
     */
    public function runRunners($deployment)
    {
        $this->deployment = $deployment;

        $this->runPreRunners();

        $runners = config('sanity.runners', [
            'unit'  => true,
            'dusk'   => true,
            'style'  => true,
            'points' => true,
        ]);

        if ($runners['unit'] === true) {
            $this->runUnitTests();
        }

        if ($runners['dusk'] === true) {
            $this->runDuskTests();
        }

        if ($runners['style'] === true) {
            $this->runStyleTests();
        }

        if ($runners['points'] === true) {
            $this->updatePointsLeaderboard();
        }

        $this->runPostRunners();
    }

    /**
     * Tasks to run before running tests.
     *
     * @return void
     */
    public function runPreRunners()
    {
        $preRunners = config('sanity.pre-runners', []);

        if (count($preRunners)) {
            foreach ($preRunners as $runner) {
                if (class_exists($runner)) {
                    (new $runner())->run($this->deployment);
                }
            }
        }
    }

    /**
     * Tasks to run after running tests.
     *
     * @return void
     */
    public function runPostRunners()
    {
        $postRunners = config('sanity.post-runners', []);

        if (count($postRunners)) {
            foreach ($postRunners as $runner) {
                if (class_exists($runner)) {
                    (new $runner())->run($this->deployment);
                }
            }
        }
    }

    /**
     * Run style tests.
     *
     * @return void
     */
    public function runStyleTests()
    {
        $phpcsPath = config('phpcsbin', base_path('vendor/bin/phpcs'));

        $result = json_decode(exec("php {$phpcsPath} --report=json"), true);

        $passing = $result['totals']['errors'] == 0;

        if ($passing && config('sanity.strictStyle')) {
            $passing = $result['totals']['warnings'] == 0;
        }

        $passingBefore = $this->cache->get('sanity.status.style', false) == 'PASSING';

        $this->cache->forever('sanity.status.style', $passing ? 'PASSING' : 'FAILING');

        $changed = $passingBefore != $passing;

        if ($passing) {
            if (!$passingBefore) {
                $this->cache->forever('sanity.fixer.style', $this->deployment);
            }
            event(new StyleSucceeded($this->getFixer('unit'), $this->getDestroyer('unit'), $result, $passingBefore != $passing));
        } else {
            if ($passingBefore) {
                $this->cache->forever('sanity.destroyer.style', $this->deployment);
            }
            event(new StyleFailed($this->getFixer('unit'), $this->getDestroyer('unit'), $result, $passingBefore != $passing));
        }
    }

    /**
     * Run unit tests.
     *
     * @return void
     */
    public function runUnitTests()
    {
        $phpunitPath = config('php-unit-bin', base_path('vendor/bin/phpunit'));
        $phpunitXmlPath = config('php-unit-xml', base_path('phpunit.xml'));

        exec("php {$phpunitPath} -c {$phpunitXmlPath}", $result, $code);

        $passing = $code == 0;

        $passingBefore = $this->cache->get('sanity.status.unit', false) == 'PASSING';

        $this->cache->forever('sanity.status.unit', $passing ? 'PASSING' : 'FAILING');

        $changed = $passingBefore != $passing;

        if ($passing) {
            if (!$passingBefore) {
                $this->cache->forever('sanity.fixer.unit', $this->deployment);
            }
            event(new UnitSucceeded($this->getFixer('unit'), $this->getDestroyer('unit'), $result, $changed));
        } else {
            if ($passingBefore) {
                $this->cache->forever('sanity.destroyer.unit', $this->deployment);
            }
            event(new UnitFailed($this->getFixer('unit'), $this->getDestroyer('unit'), $result, $changed));
        }
    }

    /**
     * Run Dusk tests.
     *
     * @return void
     */
    public function runDuskTests()
    {
        $phpunitPath = config('php-unit-bin', base_path('vendor/bin/phpunit'));
        $phpunitXmlPath = config('php-unit-dusk-xml', base_path('phpunit.dusk.xml'));

        exec("php {$phpunitPath} -c {$phpunitXmlPath}", $result, $code);

        $passing = $code == 0;

        $passingBefore = $this->cache->get('sanity.status.dusk', false) == 'PASSING';

        $this->cache->forever('sanity.status.dusk', $passing ? 'PASSING' : 'FAILING');

        $changed = $passingBefore != $passing;

        if ($passing) {
            if (!$passingBefore) {
                $this->cache->forever('sanity.fixer.dusk', $this->deployment);
            }
            event(new DuskSucceeded($this->getFixer('unit'), $this->getDestroyer('unit'), $result, $changed));
        } else {
            if ($passingBefore) {
                $this->cache->forever('sanity.destroyer.dusk', $this->deployment);
            }
            event(new DuskFailed($this->getFixer('unit'), $this->getDestroyer('unit'), $result, $changed));
        }
    }

    /**
     * Update points leaderboard.
     *
     * @return void
     */
    public function updatePointsLeaderboard()
    {
    }

    /**
     * Get most last known destroyer of tests type.
     *
     * @param string $test The type of test requesting destroyer.
     *
     * @return array
     */
    public function getDestroyer($test)
    {
        return $this->cache->get("sanity.destroyer.{$test}", []);
    }

    /**
     * Get most last known fixer of tests type.
     *
     * @param string $test The type of test requesting fixer.
     *
     * @return array
     */
    public function getFixer($test)
    {
        return $this->cache->get("sanity.fixer.{$test}", []);
    }
}
