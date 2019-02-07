<?php

Route::post(config('sanity.endpoints.forge.webhook', '/sanity/forge'), function () {
    \Facades\Sanity\Factory::runRunners();
});

Route::get(config('sanity.routes.tests', '/sanity/badges/tests.svg'), function () {
    return \Facades\Sanity\Badges::tests();
});

Route::get(config('sanity.routes.standards', '/sanity/badges/standards.svg'), function () {
    return \Facades\Sanity\Badges::standards();
});

Route::get(config('sanity.routes.dusk', '/sanity/badges/dusk.svg'), function () {
    return \Facades\Sanity\Badges::dusk();
});
