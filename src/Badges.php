<?php

namespace Sanity;

use Illuminate\Support\Facades\Cache;
use Zttp\Zttp;

class Badges
{
    const BADGE_URL = 'https://img.shields.io/badge/%s-%s-%s.svg?%s';

    /**
     * Cache instance.
     *
     * @var \lluminate\Cache\CacheManager
     */
    private $cache;

    /**
     * Create new instance of Badges.
     *
     * @return void
     */
    public function __construct()
    {
        $this->cache = Cache::store(config('sanity.cache'), 'file');
    }

    /**
     * Get badge for runner instance.
     *
     * @return void
     */
    public function get($runner, $queryString)
    {
        $label = $runner->getBadgeLabel();
        $status = $runner->getBadgeStatus();
        $colour = $runner->getBadgeColour();

        $badge = $this->getBadge($label, $status, $colour, $queryString);

        if ($badge) {
            return response($badge, 200)->header('Content-Type', 'image/svg+xml');
        }

        return abort(404);
    }

    /**
     * Get badge from cache otherwise create new.
     *
     * @return string
     */
    private function getBadge($label, $status, $colour, $queryString)
    {
        $keymd = md5($label.$status.$colour.$queryString);
        $badge = $this->cache->get("sanity.badges.{$keymd}", false);

        if (!$badge) {
            $badge = $this->getNewBadge($label, $status, $colour, $queryString);
        }

        return $badge;
    }

    /**
     * Fetch badge from shields.io.
     *
     * @return string|bool
     */
    private function getNewBadge($label, $status, $colour, $queryString)
    {
        $url = sprintf(self::BADGE_URL, $label, $status, $colour, $queryString);

        $res = Zttp::get($url);

        if ($res->isOk()) {
            $badge = $res->body();
            $keymd = md5($label.$status.$colour.$queryString);
            $this->cache->forever("sanity.badges.{$keymd}", $badge);

            return $badge;
        }

        return false;
    }

    /**
     * Get the actual badge and cache it.
     *
     * @return \Illuminate\Routing\ResponseFactory
     */
    private function badgeResponse($label, $status, $colour)
    {
        $opt = request()->getQueryString();
        $url = "https://img.shields.io/badge/{$label}-{$status}-{$colour}.svg?{$opt}";
        $md5 = md5($url);
        $key = "sanity.badges.{$md5}";

        if ($this->cache->has($key)) {
            $badge = $this->cache->get($key);
        } else {
            $badge = file_get_contents($url);
            $this->cache->forever($key, $badge);
        }

        return response($badge, 200)->header('Content-Type', 'image/svg+xml');
    }
}
