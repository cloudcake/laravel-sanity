<?php

namespace Sanity;

use Illuminate\Support\Str;

class Factory
{
    /**
     * Reference to valid runners.
     *
     * @var array
     */
    private static $runners = [];

    /**
     * Create new instance of Badges.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setupRunners();
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
     * Get runner badge.
     *
     * @return \Illuminate\Routing\ResponseFactory
     */
    public static function badge($runnerName, $queryString = '')
    {
        $runner = self::$runners[Str::slug($runnerName)] ?? false;

        if ($runner) {
            return \Facades\Sanity\Badges::get($runner, $queryString);
        }

        return abort(404);
    }

    /**
     * Create instantiations of valid runners.
     *
     * @return void
     */
    private function setupRunners()
    {
        $runners = config('sanity.runners', []);

        foreach ($runners as $runner) {
            if (is_subclass_of($runner, \Sanity\Runners\Runner::class)) {
                self::$runners[Str::slug(($instance = new $runner())->getName())] = $instance;
            }
        }
    }

    /**
     * Run all configured tasks.
     *
     * @return void
     */
    public function runRunners($data = null)
    {
        $this->deployment = $data ?? file_get_contents(__DIR__.'/Fixtures/forge.json');

        $this->checkEnvironment();
        $this->runPreRunners();

        foreach (self::$runners as $runner) {
            $runner->runNow();
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
     * Make sure we're running this on an allowed environment.
     *
     * @return void
     */
    private function checkEnvironment()
    {
        $environmentCurrent  = env('APP_ENV', 'production');
        $environmentsAllowed = config('environments', ['local', 'testing']);

        if (!in_array($environmentCurrent, $environmentsAllowed, true)) {
            exit;
        }
    }
}
