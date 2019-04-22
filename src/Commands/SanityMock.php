<?php

namespace Sanity\Commands;

use Illuminate\Console\Command;

class SanityMock extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sanity:mock';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Sanity checks as they it received a payload from Laravel Forge';

    /**
     * Handle the command.
     *
     * @return void
     */
    public function handle()
    {
        \Facades\Sanity\Factory::runRunners([]);
    }
}
