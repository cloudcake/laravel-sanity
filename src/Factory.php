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
    public static $runners = [];

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
     * Create instantiations of valid runners.
     *
     * @throws Exception If a duplicate runner name is found.
     *
     * @return void
     */
    private function setupRunners()
    {
        $runners = config('sanity.runners', []);

        foreach ($runners as $runner) {
            if (is_subclass_of($runner, \Sanity\Runners\Runner::class)) {
                $runnerInstance = new $runner();
                $runnerKey = $runnerInstance->getKeyName();

                if (isset(self::$runners[$runnerKey])) {
                    throw new \Exception("Duplicate runner name defined: {$runnerInstance->getName()}");
                }

                self::$runners[$runnerKey] = $runnerInstance;
            }
        }
    }

    /**
     * Run all configured tasks.
     *
     * @return void
     */
    public function runRunners($commit)
    {
        $this->checkEnvironment();
        $this->runPreRunners();

        // Stat collectors must run last
        self::$runners = collect(self::$runners)->sortBy(function ($runner, $s) {
            return $runner->collectsStats();
        })->all();

        foreach (self::$runners as $runner) {
            $runner->runNow($commit);
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
        $environmentCurrent = env('APP_ENV', 'production');
        $environmentsAllowed = config('environments', ['local', 'testing']);

        if (!in_array($environmentCurrent, $environmentsAllowed, true)) {
            exit;
        }
    }

    /**
     * Get runner badge.
     *
     * @return \Illuminate\Routing\ResponseFactory
     */
    public function badge($runnerName, $queryString = '')
    {
        $runner = self::$runners[Str::slug($runnerName)] ?? false;

        if ($runner) {
            return \Facades\Sanity\Badges::get($runner, $queryString);
        }

        return abort(404);
    }

    /**
     * Get runner results.
     *
     * @param string $runnerName Name of the runner.
     *
     * @return \Illuminate\Routing\ResponseFactory
     */
    public function results($runnerName)
    {
        if (auth()->user()) {
            $result = false;
            $runner = self::$runners[Str::slug($runnerName)] ?? false;

            if ($runner) {
                $result = $this->formatResult($runner->getResults());
            }

            if ($result) {
                return $result;
            }
        }

        return abort(404);
    }

    /**
     * Format runner results.
     *
     * @param array $result The result array to format.
     *
     * @return array
     */
    private function formatResult($result)
    {
        if (($format = strtoupper(request()->query('format', 'array')))) {
            switch ($format) {
              case 'JSON':
                $result = json_encode($result);
                break;
            }
        }

        return $result;
    }
}
