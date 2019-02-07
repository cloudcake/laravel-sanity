<?php

namespace Sanity\Console\Commands;

use Laravel\Dusk\Console\DuskCommand;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

class SanityDusk extends DuskCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sanity:dusk {--without-tty : Disable output to TTY}';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->purgeScreenshots();
        $this->purgeConsoleLogs();

        $options = array_slice(['artisan', 'sanity:dusk'], $this->option('without-tty') ? 3 : 2);

        return $this->withDuskEnvironment(function () use ($options) {
            $process = (new Process(array_merge(
                $this->binary(),
                $this->phpunitArguments($options)
            )))->setTimeout(null);

            try {
                $process->setTty(!$this->option('without-tty'));
            } catch (RuntimeException $e) {
                $this->output->writeln('Warning: '.$e->getMessage());
            }

            return $process->run(function ($type, $line) {
                $this->output->write($line);
            });
        });
    }
}
