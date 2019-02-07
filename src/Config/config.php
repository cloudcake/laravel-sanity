<?php

return [

  /*
  |--------------------------------------------------------------------------
  | Forge Webhook
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
  | Routes
  |--------------------------------------------------------------------------
  |
  | You can dynamically define your desired routes for each of Sanity's
  | endpoints in the event that there are conflicts or you wish to use
  | something a little more secretive.
  |
  */

  'routes' => [
      'tests'     => '/sanity/badges/tests.svg',
      'dusk'      => '/sanity/badges/dusk.svg',
      'standards' => '/sanity/badges/standards.svg',
  ],

  /*
  |--------------------------------------------------------------------------
  | Enabled Runners
  |--------------------------------------------------------------------------
  |
  | Define which runners should be run with a boolean value. Any badges that
  | are called for disabled runners will return as 'not running' or the last
  | known state before the runner was disabled.
  |
  */

  'runners' => [
      'tests'     => true,
      'dusk'      => false,
      'standards' => true,
  ],

  /*
  |--------------------------------------------------------------------------
  | Subscribers
  |--------------------------------------------------------------------------
  |
  | By default Sanity will catch its own events, replacing the below subscribers
  | with your own will allow your application to catch these events and handle
  | them as you wish, perhaps with email and/or slack notifications.
  |
  | If you're unfamiliar with subscribers, please see the Laravel docs here:
  | https://laravel.com/docs/master/events#event-subscribers
  |
  | Each event on every subscriber will contain a public `$results` array with
  | an addition `$passing` boolean indicating whether the event finished with
  | success.
  |
  */

  'subscribers' => [
    'Sanity\Events\StandardsFinished' => 'Sanity\Subscriber@onStandardsFinished',
    'Sanity\Events\UnitTestsFinished' => 'Sanity\Subscriber@onUnitTestsFinished',
    'Sanity\Events\DuskTestsFinished' => 'Sanity\Subscriber@onDuskTestsFinished',
  ],

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

  'preRunners' => [
    \App\MyPreRunnder::class,
  ],

  /*
  |--------------------------------------------------------------------------
  | Binary & Config Paths
  |--------------------------------------------------------------------------
  |
  | If your PHPUnit and/or PHPCS binaries or config files are not in the
  | default locations, you must update their paths here.
  |
  */

  'phpunitbit' => base_path('vendor/bin/phpunit'),

  'phpunitxml' => base_path('phpunit.xml'),

  'phpcsbin' => base_path('vendor/bin/phpcs'),

];
