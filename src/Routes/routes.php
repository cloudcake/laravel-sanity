<?php

Route::post(config('sanity.forge', '/sanity/forge'), function () {
    return \Facades\Sanity\Factory::runRunners(request()->all());
});

Route::get(config('sanity.badges', '/sanity/badges/{runner}.svg'), function ($runner) {
    return Facades\Sanity\Factory::badge($runner, request()->getQueryString());
});
