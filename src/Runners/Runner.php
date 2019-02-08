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
     * Indicator of the current runner status.
     *
     * @var integer|null
     */
    private $state;

    /**
     * Container for results.
     *
     * @var array
     */
    private $results = [];

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
    protected $badgeValuePending = 'pending';

    /**
     * Indicate whether or not this runner should fire events.
     *
     * @var boolean
     */
    protected $shouldFireEvents = false;

    /**
     * Construct instance of runner.
     *
     * @return self
     */
    public function __construct()
    {
        $this->cache = Cache::store(config('sanity.cache'), 'file');
    }

    /**
     * Execute runner.
     *
     * @return self
     */
    public function runNow()
    {
        try {
            $this->run();
        } catch (\Exception $e) {
            $this->failed();
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
    public function passed()
    {
        $this->state = 1;
        $this->cacheState();

        return $this;
    }

    /**
     * Set runner as failed.
     *
     * @return self
     */
    public function failed()
    {
        $this->state = 0;
        $this->cacheState();

        return $this;
    }

    /**
     * Return true if runner is passing.
     *
     * @return boolean
     */
    public function passing()
    {
        return $this->state == 1;
    }

    /**
     * Return true if runner is failing.
     *
     * @return boolean
     */
    public function failing()
    {
        return $this->state == 0;
    }

    /**
     * Return true if runner has not run.
     *
     * @return boolean
     */
    public function hasntRun()
    {
        return $this->state == -1 || is_null($this->state);
    }

    /**
     * Set output results.
     *
     * @return self
     */
    public function setResults(array $results)
    {
        $this->results = $results;

        return $this;
    }

    /**
     * Get output results.
     *
     * @return array
     */
    public function getResults()
    {
        return $this->results;
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
     * Get key name.
     *
     * @return string
     */
    public function getKeyName()
    {
        return Str::slug($this->name);
    }

    /**
     * Get badge label.
     *
     * @return string
     */
    public function getBadgeStatus()
    {
        $status = $this->badgeValuePending;

        if (is_null($this->state)) {
            $this->state = $this->cache->get("sanity.states.{$this->getKeyName()}", -1);
        }

        if ($this->passing()) {
            $status = $this->badgeValuePassing;
        } elseif ($this->failing()) {
            $status = $this->badgeValueFailing;
        }

        return rawurlencode($status);
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
        $this->cache->forever("sanity.states.{$this->getKeyName()}", $this->state);

        return $this;
    }
}
