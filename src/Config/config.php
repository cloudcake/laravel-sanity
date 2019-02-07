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
      'unit'   => '/sanity/badges/unit.svg',
      'dusk'   => '/sanity/badges/dusk.svg',
      'style'  => '/sanity/badges/style.svg',
      'points' => '/sanity/badges/points.svg',
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
  | Dusk is disabled by default due to it's additional required setup. See
  | the Sanity documentation.
  |
  */

  'runners' => [
      'tests'  => true,
      'dusk'   => false,
      'style'  => true,
      'points' => true,
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

  /*
  |--------------------------------------------------------------------------
  | Binary & Config Paths
  |--------------------------------------------------------------------------
  |
  | If your PHPUnit and/or PHPCS binaries or config files are not in the
  | default locations, you must update their paths here.
  |
  */

  'php-unit-bin' => base_path('vendor/bin/phpunit'),

  'php-unit-xml' => base_path('phpunit.xml'),

  'php-unit-dusk-xml' => base_path('phpunit.dusk.xml'),

  'php-cs-bin' => base_path('vendor/bin/phpcs'),

  /*
  |--------------------------------------------------------------------------
  | Strict Style
  |--------------------------------------------------------------------------
  |
  | If set to true, when a style succeeds with no errors but contains warnings,
  | mark the test as a failure. Super strict mode.
  |
  */

  'strictStyle' => false,

];
