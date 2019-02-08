<?php

namespace Sanity;

use Illuminate\Support\Facades\Cache;

class Badges
{
    /**
     * Cache instance.
     *
     * @var \lluminate\Cache\CacheManager
     */
    private $cache;

    const LABEL_TESTS = 'tests';
    const LABEL_STANDARDS = 'style';
    const LABEL_DUSK = 'dusk';

    const VALUE_DEFAULT = 'not running';

    const DEFAULT_COLOUR = '989898';
    const PASSING_COLOUR = '99cc00';
    const FAILING_COLOUR = 'c53232';

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
     * Get unit tests badge.
     *
     * @return \Illuminate\Routing\ResponseFactory
     */
    public function unit()
    {
        $label = rawurlencode(self::LABEL_TESTS);
        $status = rawurlencode($this->cache->get('sanity.status.tests', self::VALUE_DEFAULT));
        $colour = $this->getColourFor($status);

        return $this->respondWithBadge($label, $status, $colour);
    }

    /**
     * Get coding styles badge.
     *
     * @return \Illuminate\Routing\ResponseFactory
     */
    public function style()
    {
        $label = rawurlencode(self::LABEL_STANDARDS);
        $status = rawurlencode($this->cache->get('sanity.status.style', self::VALUE_DEFAULT));
        $colour = $this->getColourFor($status);

        return $this->respondWithBadge($label, $status, $colour);
    }

    /**
     * Get dusk tests badge.
     *
     * @return \Illuminate\Routing\ResponseFactory
     */
    public function dusk()
    {
        $label = rawurlencode(self::LABEL_DUSK);
        $status = rawurlencode($this->cache->get('sanity.status.dusk', self::VALUE_DEFAULT));
        $colour = $this->getColourFor($status);

        return $this->respondWithBadge($label, $status, $colour);
    }

    /**
     * Get colour of badge background based on status.
     *
     * @param string $status
     *
     * @return string
     */
    private function getColourFor($status)
    {
        switch ($status) {
          case 'PASSING':
            $colour = self::PASSING_COLOUR;
            break;

          case 'FAILING':
            $colour = self::FAILING_COLOUR;
            break;

          default:
            $colour = self::DEFAULT_COLOUR;
            break;
        }

        return $colour;
    }

    /**
     * Get the actual badge and cache it.
     *
     * @return \Illuminate\Routing\ResponseFactory
     */
    private function respondWithBadge($label, $status, $colour)
    {
        $status = strtolower($status);

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
