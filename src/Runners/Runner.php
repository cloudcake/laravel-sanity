<?php

namespace Sanity\Runners;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class Runner
{
    /**
     * Cache instance.
     *
     * @var \lluminate\Cache\CacheManager
     */
    private $cache;

    /**
     * Existing cache information.
     *
     * @var array|null
     */
    private $store;

    /**
     * The commit information that triggered the test.
     *
     * @var array|null
     */
    private $commit;

    /**
     * Key name of the runner.
     *
     * @var string|null
     */
    private $keyName;

    /**
     * Identifier for the runner.
     *
     * @var string
     */
    protected $name = 'Runner';

    /**
     * Label to display for the badge.
     *
     * @var string
     */
    protected $badgeLabel = 'Runner';

    /**
     * Label to display for the badge.
     *
     * @var string
     */
    protected $badgeColourPassing = '99cc00';

    /**
     * Label to display for the badge.
     *
     * @var string
     */
    protected $badgeColourFailing = 'c53232';

    /**
     * Label to display for the badge.
     *
     * @var string
     */
    protected $badgeColourUnknown = '989898';

    /**
     * Label to display for passing runner.
     *
     * @var string
     */
    protected $badgeValuePassing = 'passing';

    /**
     * Label to display for passing runner.
     *
     * @var string
     */
    protected $badgeValueFailing = 'failing';

    /**
     * Label to display for pending runner.
     *
     * @var string
     */
    protected $badgeValueUnknown = 'pending';

    /**
     * Indicate whether or not this runner should fire events.
     *
     * @var bool
     */
    protected $shouldFireEvents = true;

    /**
     * Indicate whether this runner collects stats.
     *
     * @var bool
     */
    protected $collectsStats = false;

    /**
     * Construct instance of runner.
     *
     * @return self
     */
    public function __construct()
    {
        $this->cache = Cache::store(config('sanity.cache'), 'file');
        $this->store = $this->cache->get("sanity.{$this->getKeyName()}", [
            'state'     => -1,
            'butcher'   => false,
            'saviour'   => false,
            'saved'     => false,
            'butchered' => false,
            'results'   => [],
        ]);
    }

    /**
     * Execute runner.
     *
     * @return self
     */
    public function runNow(array $commit)
    {
        $this->commit = $commit;

        try {
            $this->run();

            if ($this->collectsStats()) {
                $this->markAsPassed();
            }
        } catch (\Exception $e) {
            $this->markAsFailed();
            $this->setResults([$e->getMessage()]);
        }

        $this->fireEvents();
    }

    /**
     * Runner execution.
     *
     * @return void
     */
    protected function run() : void
    {
        // Subclass execution.
    }

    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Set runner as passing.
     *
     * @return self
     */
    public function markAsPassed()
    {
        if ($this->failing() || $this->hasntRun()) {
            $this->store['saviour'] = $this->commit;
            $this->store['saved'] = true;
            $this->store['butchered'] = false;
        } else {
            $this->store['saved'] = false;
            $this->store['butchered'] = false;
        }

        $this->store['state'] = 1;

        $this->cacheState();

        return $this;
    }

    /**
     * Set runner as failed.
     *
     * @return self
     */
    public function markAsFailed()
    {
        if ($this->passing()) {
            $this->store['butcher'] = $this->commit;
            $this->store['saved'] = false;
            $this->store['butchered'] = true;
        } else {
            $this->store['saved'] = false;
            $this->store['butchered'] = false;
        }

        $this->store['state'] = 0;

        $this->cacheState();

        return $this;
    }

    /**
     * Return true if runner is passing.
     *
     * @return bool
     */
    public function passing()
    {
        return $this->store['state'] == 1;
    }

    /**
     * Return true if runner is failing.
     *
     * @return bool
     */
    public function failing()
    {
        return $this->store['state'] == 0;
    }

    /**
     * Return true if runner has not run.
     *
     * @return bool
     */
    public function hasntRun()
    {
        return $this->store['state'] == -1;
    }

    /**
     * Set output results.
     *
     * @return self
     */
    public function setResults(array $results)
    {
        $this->store['results'] = $results;

        $this->cacheState();

        return $this;
    }

    /**
     * Get output results.
     *
     * @return array
     */
    public function getResults()
    {
        return $this->store['results'];
    }

    /**
     * Get the current commit information.
     *
     * @return array
     */
    public function getCommit()
    {
        return $this->commit;
    }

    /**
     * Get key name.
     *
     * @return string
     */
    public function getKeyName()
    {
        if (!$this->keyName) {
            $this->keyName = Str::slug($this->name);
        }

        return $this->keyName;
    }

    /**
     * Get badge label.
     *
     * @return string
     */
    public function getBadgeColour()
    {
        if ($this->passing()) {
            return $this->badgeColourPassing;
        }

        if ($this->failing()) {
            return $this->badgeColourFailing;
        }

        return $this->badgeColourUnknown;
    }

    /**
     * Get badge label.
     *
     * @return string
     */
    public function getBadgeLabel()
    {
        return rawurlencode($this->badgeLabel);
    }

    /**
     * Get badge status.
     *
     * @return string
     */
    public function getBadgeStatus()
    {
        $status = $this->badgeValueUnknown;

        if ($this->passing()) {
            $status = $this->badgeValuePassing;
        } elseif ($this->failing()) {
            $status = $this->badgeValueFailing;
        }

        return rawurlencode($status);
    }

    /**
     * Return true if the runner has changed to failed afer this run.
     *
     * @return bool
     */
    public function wasButchered()
    {
        return $this->store['butchered'] ?? false;
    }

    /**
     * Return true if the runner has changed to passing afer this run.
     *
     * @return bool
     */
    public function wasSaved()
    {
        return $this->store['saved'] ?? false;
    }

    /**
     * Return commit that last broke the test.
     *
     * @return array
     */
    public function getButcher()
    {
        return $this->store['butcher'];
    }

    /**
     * Return commit that last fixed the test.
     *
     * @return array
     */
    public function getSaviour()
    {
        return $this->store['butcher'];
    }

    /**
     * Return number of points the test adds/substracts.
     *
     * @return integer
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Return true if this runner collects stats.
     *
     * @return bool
     */
    public function collectsStats()
    {
        return $this->collectsStats;
    }

    /**
     * Fire runner events, if configured.
     *
     * @return self
     */
    private function fireEvents()
    {
        if ($this->shouldFireEvents) {
            event(new \Sanity\Events\RunnerEvent($this));
        }
    }

    /**
     * Store the current state of the runner.
     *
     * @return self
     */
    private function cacheState()
    {
        if ($this->store) {
            $this->cache->forever("sanity.{$this->getKeyName()}", $this->store);
        }

        return $this;
    }
}
