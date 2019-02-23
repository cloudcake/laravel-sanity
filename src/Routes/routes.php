<?php

Route::post(config('sanity.webhook', '/sanity/build'), function () {
    return \Facades\Sanity\Factory::runRunners(request()->all());
});

Route::get(config('sanity.badges', '/sanity/badges/{runner}.svg'), function ($runner) {
    return Facades\Sanity\Factory::badge($runner, request()->getQueryString());
});

Route::get(config('sanity.results', '/sanity/results/{runner}'), function ($runner) {
    return Facades\Sanity\Factory::results($runner);
});
