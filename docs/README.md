<h6 align="center">
    <img src="https://github.com/stephenlake/laravel-sanity/raw/master/docs/assets/laravel-sanity-banner.png"/>
</h6>

<h6 align="center">
    Self hosted in-app test and coding standards automation for Laravel Forge. Now with badges!
</h6>

# Getting Started

## Install the package via composer

    composer require stephenlake/laravel-sanity

## Register the service provider

This package makes use of Laravel's auto-discovery. If you are an using earlier version of Laravel (&lt; 5.4) you will need to manually register the service provider:

Add `Sanity\SanityServiceProvider::class` to the `providers` array in `config/app.php`.

## Publish configuration

`php artisan vendor:publish --provider="Sanity\SanityServiceProvider" --tag="config"`

This will publish `sanity.php` to your `config/` path and a `phpcs.xml` config in your base app path.

## Install Sanity's Routes

Generally packages would load their routes automatically, however Sanity allows you to customise your routes in your `config/sanity.php` config and wrap them in groups if you like.

Open up your routes file (default: `routes/web.php`) and add:
`\Sanity\Factory::routes();`

This will load the configured routes (in `config/sanity.php`).

## Disable CSRF token verification for the `forge` route

Forge fires a payload to your server whenever it is finished deploying, this URL needs to be accessible to forge and we therefore need to disable CSRF token verification for this specific route.

Open up the `VerifyCsrfToken` class (default at `app/Http/Middleware/VerifyCsrfToken.php`) and add the forge webhook route:

    protected $except = [
        '/sanity/forge',
    ];

or you can disable with a wildcard:

    protected $except = [
        '/sanity/*',
    ];

## Configure Laravel Forge

It's important to keep in mind that this package is intended to be run on your staging/testing servers. While you may run it on production, it's highly discouraged as tests are run **after** the code has already been deployed.

### Add the Sanity forge webhook

The Sanity forge webhook is the URL that forge will fire its payload to once the code has been deployed, once received Sanity will process its tests.

Enter your full domain and the configured forge webhook endpoint into your apps forge configuration:

![forge_deployment_webhook.png](https://github.com/stephenlake/laravel-sanity/raw/master/docs/assets/forge_deployment_webhook.png)

Where `example.org` is your applications **staging/testing** domain name and `/sanity/forge` is the endpoint you have configured in `config/sanity.php`.

# Usage

## Runners

Runners are individual classes used to perform tests (and other tasks) on your code base and store the result with their state at the time of the latest commit. Runners store information like the committer, the state before and after the committer triggered the runner and whether they broke or fixed your code.

### Prepackaged Runners

Sanity is packaged with some useful predefined runners out of the box to express its awesomeness.

#### Unit Runner

[(`\Sanity\Runners\UnitTestRunner`)](https://github.com/stephenlake/laravel-sanity/blob/master/src/Runners/UnitTestRunner.php)

The unit runner runs your configured PHPUnit tests.

#### Dusk Runner

[(`\Sanity\Runners\DuskTestRunner`)](https://github.com/stephenlake/laravel-sanity/blob/master/src/Runners/DuskTestRunner.php)

The dusk runner runs your configured Laravel Dusk tests.

#### Style Runner

[(`\Sanity\Runners\StyleTestRunner`)](https://github.com/stephenlake/laravel-sanity/blob/master/src/Runners/StyleTestRunner.php)

The style runner performs a strict set of PSR (with some customizations) rules to ensure that your code format and documentation is top-notch.

#### Scoreboard Runner

[(`\Sanity\Runners\ScoreboardRunner`)](https://github.com/stephenlake/laravel-sanity/blob/master/src/Runners/ScoreboardRunner.php)

The scoreboard runner applies a points system to each pusher. When a pusher fixes, breaks or leaves other runners in the same state they were before their push, an allocated number of points (could be negative) will be associated to that user.


### Creating Custom Runners

Creating your own runners couldn't be easier. Simply create a class that extends the Sanity base runner, give it a name and provide a run method that ends with telling Sanity whether your runner succeeded or failed. Let's run through an example.

We'll create a runner that tests if the `storage` directory is writable.

#### Create your runner class

    <?php

    namespace App;

    class WritableStorageTestRunner extends \Sanity\Runners\Runner
    {
        protected $name = 'Writable Storage';

        protected $badgeLabel = 'Storage Test';

        protected function run() : void
        {
            if (is_writable(storage_path())) {
               $this->markAsPassed();
            } else {
               $this->markAsFailed();
            }
        }
    }

#### Add your runner to the config

    'runners' => [
        Sanity\Runners\UnitTestRunner::class,
        Sanity\Runners\DuskTestRunner::class,
        Sanity\Runners\StyleTestRunner::class,
        Sanity\Runners\ScoreboardRunner::class,
        App\WritableStorageTestRunner::class
    ],

#### Test your runner

Sanity comes bundled with a test command (`\Sanity\Commands\SanityMock`) which may be used to simulate a deployment payload from Laravel Forge. Run this command to test your new runner:

`php artisan sanity:mock`

It's that simple. And you magically have a badge to display, check it out by opening your configured badges endpoint, defaults to `http://localhost/sanity/badges/writable-storage.svg`!

![badge](https://img.shields.io/badge/Storage%20Test-passing-99cc00.svg)

### Available Attributes Options

The base Sanity runner class contains a number of proctected attributes to customize your runners. See the complete list below:

#### `name`

`protected $name` `string` default: `Runner`

A unique name for the runner. Must be changed. Used for mapping runners and must be unique.

#### `badgeLabel`

`protected $badgeLabel` `string` default: `Runner`

The label to display on the generated badge.

#### `badgeColourPassing`

`protected $badgeColourPassing` `string` default: `99cc00`

The colour of the badge when passing. The value should be a hex value **without the leading hash (#)**.

#### `badgeColourFailing`

`protected $badgeColourFailing` `string` default: `c53232`

The colour of the badge when failing. The value should be a hex value **without the leading hash (#)**.

#### `badgeColourUnknown`

`protected $badgeColourUnknown` `string` default: `989898`

The colour of the badge when pending. The value should be a hex value **without the leading hash (#)**.

#### `badgeValuePassing`

`protected $badgeValuePassing` `string` default: `passing`

The text to display when the runner is passing.

#### `badgeValueFailing`

`protected $badgeValueFailing` `string` default: `failing`

The text to display when the runner is failing.

#### `badgeValueUnknown`

`protected $badgeValueUnknown` `string` default: `pending`

The text to display when the runner hasn't run or is pending.

#### `shouldFireEvents`

`protected $shouldFireEvents` `boolean` default: `true`

Boolean value indicated whether or not the runner should fire success and failure events once it has been run.

#### `collectsStats`

`protected $collectsStats` `boolean` default: `false`

If set to true, the runner will run after all other runners that are not set to collect stats. This is useful when you need your runner to be executed after everything else has been run in order to collect the results of the other runners.

### Available Runner Methods

Runners' helper methods:

#### `markAsPassed()`

Mark the runner as a success.

#### `markAsFailed()`

Mark the runner as a failure.

#### `passing()`

Returns true if the runner has passed.

#### `failing()`

Returns true if the runner has failed.

#### `isCurrentlyPassing()`

Alias of `passing()`. Returns true if the runner has passed.

#### `isCurrentlyFailing()`

Alias of `failing()`. Returns true if the runner has failed.

#### `hasntRun()`

Returns true if the runner hasn't run yet.

#### `setResults(array $results)`

Stores logs in array format for the recorded runner. This is required if you wish to preset logs in your notifications.

#### `getCommit()`

Get the latest commit information from the push that triggered the runner to execute.

#### `getResults()`

Get stored logs from the runner as an array.

#### `wasJustBroken()`

Returns true if this runner was previously successful, but currently failing.

#### `wasJustFixed()`

Returns true if this runner was previously failing, but currently passing.

#### `collectsStats()`

Returns true if the runner collects stats.

## Listening for results

The configuration file contains a `subscriber` field which if undefined points to a default subscriber that listens for events. If you would like your app to listen to these events and fire off your own notifications, you may do so by creating your own subscriber class and extending `Sanity\Subscriber`.

### Create your subscriber class

Create a `SanityEventSubscriber.php` file in `app/` with content:

    <?php

    namespace App;

    use Sanity\Subscriber as SanitySubscriber;

    class SanityEventSubscriber extends SanitySubscriber;
    {
        // Event handlers discussed in the next section
    }

### Create subscriber event handlers

Next we need to setup our subscriber to listen for events. Sanity fires off events based off of the runner name and its state. If the method does not exist on your subscriber, it simply does not fire the event. All runners configured in your configuration file will automatically fire off their events.

For example, let's assume we have a runner named **UnitTest**, once the UnitTest runner has finished with success, Sanity will look for an event named on**UnitTest**Success, or on a failure it will look for an event named on**UnitTest**Failure, so we need to define these event methods:

    <?php

    namespace App;

    use Sanity\Subscriber as SanitySubscriber;

    class SanityEventSubscriber extends SanitySubscriber;
    {
        public function onUnitTestSuccess($runner)
        {
           // Handle successful runner
        }

        public function onUnitTestFailure($runner)
        {
           // Handle failed runner
        }
    }

These events should be defined for every runner you have configured in your configuration file.

**Note**: The name of the runner is not necessarily the file name prefix nor class name. You can view the name of each runner by opening their source code and viewing the `protected $name` attribute.

### Update your config

`subscriber` => `App\SanityEventSubscriber::class`

## Slack notifications

The base subscriber is bundled with a little Slack helper to assist in submitting Slack notifications via webhook URL.

    <?php

    namespace App;

    use Sanity\Subscriber as SanitySubscriber;

    class SanityEventSubscriber extends SanitySubscriber;
    {
        const MY_SLACK_WEHBOOK_URL = 'https://hooks.slack.com/services/T00000/B0000/XXXXXXXXXXX';

        public function onMyRunnerSuccess($runner)
        {
           $commit = $runner->getCommit();
           $pusher = $commit['commit_author'];

           if ($runner->wasSaved()) {
             slack(self::MY_SLACK_WEHBOOK_URL)
                ->success()
                ->title("{$pusher} fixed the tests!")
                ->text("{$pusher} committed and fixed broken tests!")
                ->send();
           } else {
             slack(self::MY_SLACK_WEHBOOK_URL)
                ->success()
                ->title("The tests are still passing!")
                ->text("{$pusher} committed without breaking the test!")
                ->send();
           }
        }

        public function onMyRunnerFailure($runner)
        {
            if ($runner->wasButchered()) {
              slack(self::MY_SLACK_WEHBOOK_URL)
                 ->danger()
                 ->title("{$pusher} broke the tests!")
                 ->text("{$pusher} committed and broke a passing test!")
                 ->send();
            } else {
              slack(self::MY_SLACK_WEHBOOK_URL)
                 ->danger()
                 ->title("The tests are still broken!")
                 ->text("{$pusher} committed without fixing the test!")
                 ->send();
            }
        }
    }

# Extended Usage

## Adding pre-runners

There may be situations where you need to run some setup before the tests commence. You may define pre-runner classes in the config within the `pre-runners` block. These classes must be instatiable and contain a public `run` method, example:

Add the `\App\MyExamplePreRunner::class` file to the `pre-runners` block in `configs/sanity.php`:

    'pre-runners' => [
        \App\MyExamplePreRunner::class,
    ],

Create the pre-runner:

        <?php

        namespace App;

        class MyExamplePreRunner
        {

          /**
           * Run pre-runner before any tests are executed.
           *
           * @param array $committer The committer that triggered the build.
           *
           * @return mixed
           */
            public function run(array $committer)
            {
                // Pre-runner code
            }
        }

## Adding post-runners

Like pre-runners, you can apply post-runners that run after tests have executed.

Add the `\App\MyExamplePostRunner::class` file to the `post-runners` block in `configs/sanity.php`:

    'post-runners' => [
      \App\MyExamplePostRunner::class,
    ],

Create the pre-runner:

    <?php

    namespace App;

    class MyExamplePostRunner
    {

      /**
       * Run post-runner after all tests are executed.
       *
       * @param array $committer The committer that triggered the build.
       *
       * @return mixed
       */
        public function run(array $committer)
        {
            // Post-runner code
        }
    }

## Modifying Style/Standards Rules

Sanity uses PHP CodeSniffer to inspect and judge your code format based on a set of customised rules in accordance to PSR. If you wish, you may edit these rules by modifying the `phpcs.xml` file within your project root (published by Sanity). Within this file you can include and exlcude paths.

For information on how to manage and modify the PHP CodeSniffer rules, view the [PHP CodeSniffer documentation](https://github.com/squizlabs/PHP_CodeSniffer/wiki).

# Badges

Sanity has badges! Thanks to [shields.io](https://shields.io) Sanity creates badges indicating the status of your applications tests. Once a badge is created, it will be cached.

## Viewing your runner badges

Badges are automatically created for every runner and may be viewed by calling the sluggified `$name` attribute on the runner, for example:

If your runner's configured name is `Green Apple Tree`, then the name to call in the badges URL will be:
`http://yourhost/sanity/badges/green-apple-tree.svg`

Example: `https://staging.example.org/sanity/badges/green-apple-tree.svg`

Which will produce (dependeing on your status):

![badge](https://img.shields.io/badge/green--apple--tree-passing-99cc00.svg)
![badge](https://img.shields.io/badge/green--apple--tree-failing-c53232.svg)
![badge](https://img.shields.io/badge/green--apple--tree-not%20running-989898.svg)

## Customizing your badges

Since Sanity makes use of shields.io, any options provided by shields.io are applicable to Sanity's badge generation. Simply append the options to the URL as query parameters, for example, let's assume our application's domain is `staging.example.org` and we're using the default configured badges endpoint of `/sanity/badges/`:

`https://staging.example.org/sanity/badges/test.svg?style=for-the-badge`:

![badge](https://img.shields.io/badge/tests-passing-99cc00.svg?style=for-the-badge)
![badge](https://img.shields.io/badge/tests-failing-c53232.svg?style=for-the-badge)
![badge](https://img.shields.io/badge/tests-not%20running-989898.svg?style=for-the-badge)

`https://staging.example.org/sanity/badges/test.svg?style=popout-square&logo=php&logoColor=white`:

![badge](https://img.shields.io/badge/tests-passing-99cc00.svg?style=popout-square&logo=php&logoColor=white)
![badge](https://img.shields.io/badge/tests-failing-c53232.svg?style=popout-square&logo=php&logoColor=white)
![badge](https://img.shields.io/badge/tests-not%20running-989898.svg?style=popout-square&logo=php&logoColor=white)

`https://staging.example.org/sanity/badges/test.svg?style=for-the-badge&logo=laravel&logoColor=white`:

![badge](https://img.shields.io/badge/tests-passing-99cc00.svg?style=for-the-badge&logo=laravel&logoColor=white)
![badge](https://img.shields.io/badge/tests-failing-c53232.svg?style=for-the-badge&logo=laravel&logoColor=white)
![badge](https://img.shields.io/badge/tests-not%20running-989898.svg?style=for-the-badge&logo=laravel&logoColor=white)

`https://staging.example.org/sanity/badges/test.svg?style=social`:

![badge](https://img.shields.io/badge/dusk-passing-99cc00.svg?style=social)
![badge](https://img.shields.io/badge/dusk-failing-c53232.svg?style=social)
![badge](https://img.shields.io/badge/dusk-not%20running-989898.svg?style=social)

## Custmizing your badge URL's

Open up `config/sanity.php` and head to the `badges` block. Here you can define the endpoints needed to be hit on your application to retrieve badges.
