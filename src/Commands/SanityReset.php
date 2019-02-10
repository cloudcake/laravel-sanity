<?php

namespace Sanity\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Sanity\Factory;

class SanityReset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sanity:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all cached objects';

    /**
     * Handle the command.
     *
     * @return void
     */
    public function handle()
    {
        $factory = new Factory();
        $cache = Cache::store(config('sanity.cache', 'file'));

        foreach (Factory::$runners as $runner) {
            $cache->forget("sanity.{$runner->getKeyName()}");
        }
    }
}
