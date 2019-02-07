<?php

namespace Sanity;

use Illuminate\Support\Facades\Log;

class Subscriber
{
    /**
     * Handle coding standards finished event.
     *
     * @param \Sanity\Events\StandardsFinished $event
     *
     * @return void
     */
    public function onStandardsFinished($event)
    {
        if ($event->passing == true) {
            Log::info('Coding standards tests are passing.', ['results' => $event->results]);
        } else {
            Log::info("Coding standards tests are failing with {$event->results['totals']['errors']} errors.", ['results' => $event->results]);
        }
    }

    /**
     * Handle unit tests finished event.
     *
     * @param \Sanity\Events\UnitTestsFinished $event
     *
     * @return void
     */
    public function onUnitTestsFinished($event)
    {
        if ($event->passing == true) {
            Log::info('Unit tests are passing.', ['results' => $event->results]);
        } else {
            Log::info('Unit tests are are failing.', ['results' => $event->results]);
        }
    }

    /**
     * Handle dusk tests finished event.
     *
     * @param \Sanity\Events\DuskTestsFinished $event
     *
     * @return void
     */
    public function onDuskTestsFinished($event)
    {
        if ($event->passing == true) {
            Log::info('Dusk tests are passing.', ['results' => $event->results]);
        } else {
            Log::info('Dusk tests are are failing.', ['results' => $event->results]);
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $subscribers = config('sanity.subscribers', []);

        foreach ($subscribers as $eventClass => $subscriber) {
            $events->listen($eventClass, $subscriber);
        }
    }
}
