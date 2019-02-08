<h6 align="center">
    <img src="https://github.com/stephenlake/laravel-sanity/raw/master/docs/assets/laravel-sanity.png" width="450"/>
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

### Event service provider

For event subscribing, add `Sanity\SanityEventServiceProvider::class` to the `providers` array in `config/app.php`.

## Publish configuration

`php artisan vendor:publish --provider="Sanity\SanityServiceProvider" --tag="config"`

This will publish `sanity.php` to your `config/` path and a `phpcs.xml` config for PHPCS in your base application path.

## Install Sanity's Routes

Generally packages would load their routes automatically, however Sanity allows you to customise your routes in your `config/sanity.php` config and protected them with groupings.

Open up your routes file (default: `routes/web.php`) and add:
`\Sanity\Factory::routes();`

This will load the configured routes (in `config/sanity.php`).

## Disable CSRF token verification for the `forge` route

Forge fires a payload to your server whenever it is finished deploying, this URL needs to be accessible to forge and we therefore need to disable CSRF token verification for this specific route.

Open up the `VerifyCsrfToken` class, by default at `app/Http/Middleware/VerifyCsrfToken` and add the forge webhook route:

```
protected $except = [
    '/sanity/forge',
];
```

or you can disable with a wildcard:

```
protected $except = [
    '/sanity/*',
];
```

## Configure Laravel Forge

It's important to keep in mind that this package is intended to be run on your staging/testing servers. While you may run it on production, it's highly discouraged as tests are run **after** the code has already been deployed.

### Add the Sanity forge webhook

The Sanity forge webhook is the URL that forge will fire its payload to once the code has been deployed, once received Sanity will process its tests.

Enter your full domain and the configured forge webhook endpoint into your apps forge configuration:

![forge_deployment_webhook.png](https://github.com/stephenlake/laravel-sanity/raw/master/docs/assets/forge_deployment_webhook.png)

Where `example.org` is your applications **staging/testing** domain name and `/sanity/forge` is the endpoint you have configured in `config/sanity.php`.

# Usage

Once installation is setup, you can test by triggering a manual deploy on forge. By default when tests are completed Sanity will print to your log file with the status of each test.

## Listening for results

The configuration file contains a `subscribers` field which if undefined points to a default subscriber that listens for events. If you would like your app to listen to these events and fire off your own notifications,
you may do so by creating your own subscriber class and extending `Sanity\Subscriber`.

### Example setup:

#### Edit the config

`subscriber` => `App\SanityEventSubscriber`

#### Create your subscriber class

Create a `SanityEventSubscriber.php` file in `app/` with content:

```
<?php

namespace App;

class SanityEventSubscriber
{
    /**
     * Handle the Style success event.
     *
     * @param array   $committer The committer that triggered the build.
     * @param array   $fixer     The last known successful commiter.
     * @param array   $destroyer The last known destroyer of success.
     * @param array   $logs      The list of output logs from the runner.
     * @param boolean $changed   Indicates whether the result changed from the last run.
     *
     * @return void
     */
    protected function onStyleSuccess($committer, $fixer, $destroyer, $logs, $changed)
    {
    }

    /**
     * Handle the Style failure event.
     *
     * @param array   $committer The committer that triggered the build.
     * @param array   $fixer     The last known successful commiter.
     * @param array   $destroyer The last known destroyer of success.
     * @param array   $logs      The list of output logs from the runner.
     * @param boolean $changed   Indicates whether the result changed from the last run.
     *
     * @return void
     */
    protected function onStyleFailure($committer, $fixer, $destroyer, $logs, $changed)
    {
    }

    /**
     * Handle the Unit success event.
     *
     * @param array   $committer The committer that triggered the build.
     * @param array   $fixer     The last known successful commiter.
     * @param array   $destroyer The last known destroyer of success.
     * @param array   $logs      The list of output logs from the runner.
     * @param boolean $changed   Indicates whether the result changed from the last run.
     *
     * @return void
     */
    protected function onUnitSuccess($committer, $fixer, $destroyer, $logs, $changed)
    {
    }

    /**
     * Handle the Unit failure event.
     *
     * @param array   $committer The committer that triggered the build.
     * @param array   $fixer     The last known successful commiter.
     * @param array   $destroyer The last known destroyer of success.
     * @param array   $logs      The list of output logs from the runner.
     * @param boolean $changed   Indicates whether the result changed from the last run.
     *
     * @return void
     */
    protected function onUnitFailure($committer, $fixer, $destroyer, $logs, $changed)
    {
    }

    /**
     * Handle the Dusk success event.
     *
     * @param array   $committer The committer that triggered the build.
     * @param array   $fixer     The last known successful commiter.
     * @param array   $destroyer The last known destroyer of success.
     * @param array   $logs      The list of output logs from the runner.
     * @param boolean $changed   Indicates whether the result changed from the last run.
     *
     * @return void
     */
    protected function onDuskSuccess($committer, $fixer, $destroyer, $logs, $changed)
    {
    }

    /**
     * Handle the Dusk failure event.
     *
     * @param array   $committer The committer that triggered the build.
     * @param array   $fixer     The last known successful commiter.
     * @param array   $destroyer The last known destroyer of success.
     * @param array   $logs      The list of output logs from the runner.
     * @param boolean $changed   Indicates whether the result changed from the last run.
     *
     * @return void
     */
    protected function onDuskFailure($committer, $fixer, $destroyer, $logs, $changed)
    {
    }
}
```

And you're done. Now whenever a test is finished, your subscriber will be called instead of Sanity's default subscriber.

# Advanced Usage

## Adding pre-runners

There may be situations where you need to run some setup before the tests commence. You may define pre-runner classes in the config within the `pre-runners` block. These classes must be instatiable and contain a public `run` method, example:

Add the `\App\MyExamplePreRunner::class` file to the `pre-runners` block in `configs/sanity.php`:

```
'pre-runners' => [
  \App\MyExamplePreRunner::class,
],
```

Create the pre-runner:

```
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
```

## Adding post-runners

Like pre-runners, you can apply post-runners that run after tests have executed.

Add the `\App\MyExamplePostRunner::class` file to the `post-runners` block in `configs/sanity.php`:

```
'post-runners' => [
  \App\MyExamplePostRunner::class,
],
```

Create the pre-runner:

```
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
```

## Modifying Code Sniffer Rules

Sanity uses PHP CodeSniffer to inspect and judge your code format based on a set of customised rules in accordance to PSR. If you wish, you may edit these rules by modifying the `phpcs.xml` file within your project root (published by Sanity). Within this file you can include and exlcude paths.

For information on how to manage and modify the PHP CodeSniffer rules, view the [PHP CodeSniffer documentation](https://github.com/squizlabs/PHP_CodeSniffer/wiki).

# Badges

Sanity has badges! Thanks to [shields.io](https://shields.io) Sanity creates badges indicating the status of your applications tests. Once a badge is create, it will be cached.

## Unit Tests Badge

Hit your domain with the configured `badges->unit` endpoint:

Example: `https://staging.example.org/sanity/badges/unit.svg`

Which will produce (dependeing on your status):

![badge](https://img.shields.io/badge/tests-passing-99cc00.svg)
![badge](https://img.shields.io/badge/tests-failing-c53232.svg)
![badge](https://img.shields.io/badge/tests-not%20running-989898.svg)

### Dusk Tests Badge

Hit your domain with the configured `badges->dusk` endpoint:

Example: `https://staging.example.org/sanity/badges/dusk.svg`

Which will produce (dependeing on your status):

![badge](https://img.shields.io/badge/dusk-passing-99cc00.svg)
![badge](https://img.shields.io/badge/dusk-failing-c53232.svg)
![badge](https://img.shields.io/badge/dusk-not%20running-989898.svg)

## Style/Standards Badge

Hit your domain with the configured `badges->style` endpoint:

Example: `https://staging.example.org/sanity/badges/style.svg`

Which will produce (dependeing on your status):

![badge](https://img.shields.io/badge/coding%20standards-passing-99cc00.svg)
![badge](https://img.shields.io/badge/coding%20standards-failing-c53232.svg)
![badge](https://img.shields.io/badge/coding%20standards-not%20running-989898.svg)

## Customizing your badges

Since Sanity makes use of shields.io, any options provided by shields.io are applicable to Sanity's badge generation. Simply append the options to the URL as query parameters, for example, let's assume our application's domain is `staging.example.org` and we're using the default configured badges endpoint of `/sanity/badges/`:

Calling `https://staging.example.org/sanity/badges/test.svg?style=for-the-badge` will produce the following badge:

![badge](https://img.shields.io/badge/tests-passing-99cc00.svg?style=for-the-badge)
![badge](https://img.shields.io/badge/tests-failing-c53232.svg?style=for-the-badge)
![badge](https://img.shields.io/badge/tests-not%20running-989898.svg?style=for-the-badge)

## Custmizing your badge URL's

Open up `config/sanity.php` and head to the `badges` block. Here you can define the endpoints needed to be hit on your application to retrieve badges.
