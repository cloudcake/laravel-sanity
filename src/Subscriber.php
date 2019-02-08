<?php

namespace Sanity;

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
        $this->onStyleSuccess($e->committer, $e->fixer, $e->destroyer, $e->logs, $e->changed);
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
        $this->onStyleFailure($e->committer, $e->fixer, $e->destroyer, $e->logs, $e->changed);
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
        $this->onUnitSuccess($e->committer, $e->fixer, $e->destroyer, $e->logs, $e->changed);
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
        $this->onUnitFailure($e->committer, $e->fixer, $e->destroyer, $e->logs, $e->changed);
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
        $this->onDuskSuccess($e->committer, $e->fixer, $e->destroyer, $e->logs, $e->changed);
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
        $this->onDuskFailure($e->committer, $e->fixer, $e->destroyer, $e->logs, $e->changed);
    }

    /**
     * Handle the Style success event.
     *
     * @param array $committer The committer that triggered the build.
     * @param array $fixer     The last known successful commiter.
     * @param array $destroyer The last known destroyer of success.
     * @param array $logs      The list of output logs from the runner.
     * @param bool  $changed   Indicates whether the result changed from the last run.
     *
     * @return void
     */
    protected function onStyleSuccess($committer, $fixer, $destroyer, $logs, $changed)
    {
    }

    /**
     * Handle the Style failure event.
     *
     * @param array $committer The committer that triggered the build.
     * @param array $fixer     The last known successful commiter.
     * @param array $destroyer The last known destroyer of success.
     * @param array $logs      The list of output logs from the runner.
     * @param bool  $changed   Indicates whether the result changed from the last run.
     *
     * @return void
     */
    protected function onStyleFailure($committer, $fixer, $destroyer, $logs, $changed)
    {
    }

    /**
     * Handle the Unit success event.
     *
     * @param array $committer The committer that triggered the build.
     * @param array $fixer     The last known successful commiter.
     * @param array $destroyer The last known destroyer of success.
     * @param array $logs      The list of output logs from the runner.
     * @param bool  $changed   Indicates whether the result changed from the last run.
     *
     * @return void
     */
    protected function onUnitSuccess($committer, $fixer, $destroyer, $logs, $changed)
    {
    }

    /**
     * Handle the Unit failure event.
     *
     * @param array $committer The committer that triggered the build.
     * @param array $fixer     The last known successful commiter.
     * @param array $destroyer The last known destroyer of success.
     * @param array $logs      The list of output logs from the runner.
     * @param bool  $changed   Indicates whether the result changed from the last run.
     *
     * @return void
     */
    protected function onUnitFailure($committer, $fixer, $destroyer, $logs, $changed)
    {
    }

    /**
     * Handle the Dusk success event.
     *
     * @param array $committer The committer that triggered the build.
     * @param array $fixer     The last known successful commiter.
     * @param array $destroyer The last known destroyer of success.
     * @param array $logs      The list of output logs from the runner.
     * @param bool  $changed   Indicates whether the result changed from the last run.
     *
     * @return void
     */
    protected function onDuskSuccess($committer, $fixer, $destroyer, $logs, $changed)
    {
    }

    /**
     * Handle the Dusk failure event.
     *
     * @param array $committer The committer that triggered the build.
     * @param array $fixer     The last known successful commiter.
     * @param array $destroyer The last known destroyer of success.
     * @param array $logs      The list of output logs from the runner.
     * @param bool  $changed   Indicates whether the result changed from the last run.
     *
     * @return void
     */
    protected function onDuskFailure($committer, $fixer, $destroyer, $logs, $changed)
    {
    }

    /**
     * Get new instance of SlackMessage.
     *
     * @param string $webhook Webhook URL to post to.
     *
     * @return SlackMessage
     */
    public function slack($webhook)
    {
        return new SlackMessage($webhook);
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
