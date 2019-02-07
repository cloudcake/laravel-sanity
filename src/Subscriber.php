<?php

namespace Sanity;

use Illuminate\Support\Facades\Log;

class Subscriber
{
    /**
     * Handle the event.
     *
     * @param \Sanity\Events\StyleSucceeded $e The event being handled.
     *
     * @return void
     */
    public function styleSuccess($e)
    {
        $this->onStyleSuccess($e->fixer, $e->destroyer, $e->logs, $e->changed);
    }

    /**
     * Handle the event.
     *
     * @param \Sanity\Events\StyleFailed $e The event being handled.
     *
     * @return void
     */
    public function styleFailure($e)
    {
        $this->onStyleFailure($e->fixer, $e->destroyer, $e->logs, $e->changed);
    }

    /**
     * Handle the event.
     *
     * @param \Sanity\Events\UnitSucceeded $e The event being handled.
     *
     * @return void
     */
    public function unitSuccess(\Sanity\Events\UnitSucceeded $e)
    {
        $this->onUnitSuccess($e->fixer, $e->destroyer, $e->logs, $e->changed);
    }

    /**
     * Handle the event.
     *
     * @param \Sanity\Events\UnitFailed $e The event being handled.
     *
     * @return void
     */
    public function unitFailure(\Sanity\Events\UnitFailed $e)
    {
        $this->onUnitFailure($e->fixer, $e->destroyer, $e->logs, $e->changed);
    }

    /**
     * Handle the event.
     *
     * @param \Sanity\Events\DuskSucceeded $e The event being handled.
     *
     * @return void
     */
    public function duskSuccess(\Sanity\Events\DuskSucceeded $e)
    {
        $this->onDuskSuccess($e->fixer, $e->destroyer, $e->logs, $e->changed);
    }

    /**
     * Handle the event.
     *
     * @param \Sanity\Events\DuskFailed $e The event being handled.
     *
     * @return void
     */
    public function duskFailure(\Sanity\Events\DuskFailed $e)
    {
        $this->onDuskFailure($e->fixer, $e->destroyer, $e->logs, $e->changed);
    }

    /**
     * Handle the Style success event.
     *
     * @param array $fixer     The last known successful commiter.
     * @param array $destroyer The last known destroyer of success.
     * @param array $logs      The list of output logs from the runner.
     * @param array $changed   Indicates whether the result changed from the last run.
     *
     * @return void
     */
    protected function onStyleSuccess($fixer, $destroyer, $logs, $changed)
    {
        Log::info('Style runner succeeded', [
            'fixer'     => $fixer,
            'destroyer' => $destroyer,
            'logs'      => $logs,
            'changed'   => intval($changed)
        ]);
    }

    /**
     * Handle the Style failure event.
     *
     * @param array $fixer     The last known successful commiter.
     * @param array $destroyer The last known destroyer of success.
     * @param array $logs      The list of output logs from the runner.
     * @param array $changed   Indicates whether the result changed from the last run.
     *
     * @return void
     */
    protected function onStyleFailure($fixer, $destroyer, $logs, $changed)
    {
        Log::info('Style runner failed', [
            'fixer'     => $fixer,
            'destroyer' => $destroyer,
            'logs'      => $logs,
            'changed'   => intval($changed)
        ]);
    }

    /**
     * Handle the Unit success event.
     *
     * @param array $fixer     The last known successful commiter.
     * @param array $destroyer The last known destroyer of success.
     * @param array $logs      The list of output logs from the runner.
     * @param array $changed   Indicates whether the result changed from the last run.
     *
     * @return void
     */
    protected function onUnitSuccess($fixer, $destroyer, $logs, $changed)
    {
        Log::info('Unit runner succeeded', [
            'fixer'     => $fixer,
            'destroyer' => $destroyer,
            'logs'      => $logs,
            'changed'   => intval($changed)
        ]);
    }

    /**
     * Handle the Unit failure event.
     *
     * @param array $fixer     The last known successful commiter.
     * @param array $destroyer The last known destroyer of success.
     * @param array $logs      The list of output logs from the runner.
     * @param array $changed   Indicates whether the result changed from the last run.
     *
     * @return void
     */
    protected function onUnitFailure($fixer, $destroyer, $logs, $changed)
    {
        Log::info('Unit runner failed', [
            'fixer'     => $fixer,
            'destroyer' => $destroyer,
            'logs'      => $logs,
            'changed'   => intval($changed)
        ]);
    }

    /**
     * Handle the Dusk success event.
     *
     * @param array $fixer     The last known successful commiter.
     * @param array $destroyer The last known destroyer of success.
     * @param array $logs      The list of output logs from the runner.
     * @param array $changed   Indicates whether the result changed from the last run.
     *
     * @return void
     */
    protected function onDuskSuccess($fixer, $destroyer, $logs, $changed)
    {
        Log::info('Dusk runner succeeded', [
            'fixer'     => $fixer,
            'destroyer' => $destroyer,
            'logs'      => $logs,
            'changed'   => intval($changed)
        ]);
    }

    /**
     * Handle the Dusk failure event.
     *
     * @param array $fixer     The last known successful commiter.
     * @param array $destroyer The last known destroyer of success.
     * @param array $logs      The list of output logs from the runner.
     * @param array $changed   Indicates whether the result changed from the last run.
     *
     * @return void
     */
    protected function onDuskFailure($fixer, $destroyer, $logs, $changed)
    {
        Log::info('Dusk runner failed', [
            'fixer'     => $fixer,
            'destroyer' => $destroyer,
            'logs'      => $logs,
            'changed'   => intval($changed)
        ]);
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $subscriber = config('sanity.subscriber', get_class());

        $events->listen('Sanity\Events\StyleSucceeded', "{$subscriber}@styleSuccess");
        $events->listen('Sanity\Events\StyleFailed', "{$subscriber}@styleFailure");
        $events->listen('Sanity\Events\UnitSucceeded', "{$subscriber}@unitSuccess");
        $events->listen('Sanity\Events\UnitFailed', "{$subscriber}@unitFailure");
        $events->listen('Sanity\Events\DuskSucceeded', "{$subscriber}@duskSuccess");
        $events->listen('Sanity\Events\DuskFailed', "{$subscriber}@duskFailure");
    }
}
