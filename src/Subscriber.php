<?php

namespace Sanity;

class Subscriber
{
    /**
     * Handle the event.
     *
     * @param \Sanity\Events\RunnerEvent $e The event being handled.
     *
     * @return void
     */
    public function onRunnerFinished($e)
    {
        $runnerClass = $e->runner;
        $runnerState = $runnerClass->passing() ? 'Success' : 'Failure';
        $runnerMethod = "on{$runnerClass->getName()}{$runnerState}";

        echo 'hear event';

        if (method_exists($this, $runnerMethod)) {
            $this->$runnerMethod($runner);
        }
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

        $events->listen('Sanity\Events\RunnerEvent', "{$subscriber}@onRunnerFinished");
    }
}
