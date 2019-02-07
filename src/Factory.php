<?php

namespace Sanity;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
            'tests'     => true,
            'dusk'      => true,
            'standards' => true,
        ]);

        if ($runners['tests'] === true) {
            $this->runUnitTests();
        }

        if ($runners['dusk'] === true) {
            $this->runDuskTests();
        }

        if ($runners['standards'] === true) {
            $this->runStandards();
        }
    }

    /**
     * Tasks to run before running tests.
     *
     * @return void
     */
    public function runPreRunners()
    {
        $preRunners = config('sanity.preRunners', []);

        if (count($preRunners)) {
            foreach ($preRunners as $runner) {
                if (class_exists($runner)) {
                    try {
                        (new $runner())->run($this->deployment);
                    } catch (\Exception $e) {
                        Log::error("Sanity could not run prerunnger {$runner}: {$e->getMessage()}");
                    }
                }
            }
        }
    }

    /**
     * Run PHPCS tests.
     *
     * @return void
     */
    public function runStandards()
    {
        $phpcsPath = config('phpcsbin', base_path('vendor/bin/phpcs'));

        chmod($phpcsPath, 0755);

        $results = json_decode(exec("php {$phpcsPath} --report=json"), true);

        $passing = $results['totals']['errors'] == 0;

        $passingBefore = $this->cache->get('sanity.status.standards', false);

        $this->cache->forever('sanity.status.standards', $passing ? 'PASSING' : 'FAILING');

        event(new \Sanity\Events\StandardsFinished($results, $passing, ($passingBefore == 'PASSING'), $this->deployment));
    }

    /**
     * Run PHPUnit tests.
     *
     * @return void
     */
    public function runUnitTests()
    {
        $phpunitPath = config('phpunitbin', base_path('vendor/bin/phpunit'));
        $phpunitPathAlt = base_path('vendor/phpunit/phpunit/phpunit');
        $phpunitXmlPath = config('phpunitxml', base_path('phpunit.xml'));

        chmod($phpunitPath, 0755);
        chmod($phpunitPathAlt, 0755);

        exec("php {$phpunitPath} -c {$phpunitXmlPath}", $result, $code);

        $passing = $code == 0;

        $passingBefore = $this->cache->get('sanity.status.tests', false);

        $this->cache->forever('sanity.status.tests', $passing ? 'PASSING' : 'FAILING');

        event(new \Sanity\Events\UnitTestsFinished($result, $passing, ($passingBefore == 'PASSING'), $this->deployment));
    }

    /**
     * Run Dusk tests.
     *
     * @return void
     */
    public function runDuskTests()
    {
        exec('php '.base_path('artisan').' sanity:dusk --without-tty', $result, $code);

        $passing = $code == 0;

        $passingBefore = $this->cache->get('sanity.status.dusk', false);

        $this->cache->forever('sanity.status.dusk', $passing ? 'PASSING' : 'FAILING');

        event(new \Sanity\Events\DuskTestsFinished($result, $passing, ($passingBefore == 'PASSING'), $this->deployment));
    }
}
