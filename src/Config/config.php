<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Allowed Environments
  |--------------------------------------------------------------------------
  |
  | Set the environments Sanity is allowed to run on. While it's possible to
  | run on production, this is highly discouraged as these tests are only run
  | after the code is already deployed.
  |
  | Since a Laravel Forge payload is required to hit your endpoint to fire off
  | the tests and you cannot receive it locally unless using a third-party
  | software like ngrok, Sanity contains an artisan command to test on your
  | local environment: php artisan sanity:mock
  |
  | Options: local, testing, production
  */
  'environments' => ['local', 'testing'],

  /*
  |--------------------------------------------------------------------------
  | Forge Webhook Route Endpoint
  |--------------------------------------------------------------------------
  |
  | You may define your own forge webhook endpoint here. Change this as you
  | wish but remember to update your application's forge webhook endpoint
  | when changing this. See the documentation for more information.
  |
  | It's recommended that you make this URL somewhat secretive by making it
  | a little more verbose than it currently is (add some long hash) to prevent
  | unauthorised hits.
  |
  */

  'forge' => '/sanity/forge',

  /*
  |--------------------------------------------------------------------------
  | Badge Route Endpoint
  |--------------------------------------------------------------------------
  |
  | You can define your desired badge endpoint route if the default does not
  | suit your needs. This is the endpoint that must be hit to produce an image
  | badge for the given runner.
  |
  | If changing the route, you must include the {runner} placeholder which will
  | identify which badge to fetch.
  |
  */

  'badges' => '/sanity/badges/{runner}.svg',

  /*
  |--------------------------------------------------------------------------
  | Enabled Runners
  |--------------------------------------------------------------------------
  |
  | Define which runners should be run with a boolean value. Any badges that
  | are called for disabled runners will return as 'not running' or the last
  | known state before the runner was disabled.
  |
  | You may define your own runners here as long as it extends the base Sanity
  | runner \Sanity\Runners\Runner. See the documentation.
  |
  */

  'runners' => [
      Sanity\Runners\UnitTestRunner::class,
      Sanity\Runners\DuskTestRunner::class,
      Sanity\Runners\StyleTestRunner::class,
      Sanity\Runners\PointsRunner::class,
  ],

  /*
  |--------------------------------------------------------------------------
  | Subscriber
  |--------------------------------------------------------------------------
  |
  | By default Sanity will catch its own events. Replacing the below subscriber
  | with your own which extends the Sanity subsriber will allow your app to
  | catch these all events events and handle them as you wish, perhaps with
  | email and/or slack notifications.
  |
  | See the Sanity documentation for the list of events to cater for:
  | https://github.com/stephenlake/laravel-sanity
  |
  */

  'subscriber' => Sanity\Subscriber::class,

  /*
  |--------------------------------------------------------------------------
  | Cache
  |--------------------------------------------------------------------------
  |
  | The desired cache store to use when storing information on your app's
  | current state, ie: test results, coding standard results and commit info.
  |
  | This store must align with what's configured in your config/cache.php
  | configurations.
  |
  */

  'cache' => 'file',

  /*
  |--------------------------------------------------------------------------
  | Pre-runners
  |--------------------------------------------------------------------------
  |
  | If there's tasks you wish to run before Sanity runs its checks, you can
  | define your pre-runner classes here. Each class requires a public run()
  | method which will be called.
  |
  */

  'pre-runners' => [],

  /*
  |--------------------------------------------------------------------------
  | Post-runners
  |--------------------------------------------------------------------------
  |
  | If there's tasks you wish to run after Sanity runs its checks, you can
  | define your post-runner classes here. Each class requires a public run()
  | method which will be called.
  |
  */

  'post-runners' => [],

];
