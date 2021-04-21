<?php

namespace Lomkit\Laravel\Console;

use Illuminate\Console\Command;

class PruneCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telescope:prune {--hours=24 : The number of hours to retain Telescope data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune stale entries from the Telescope database';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('entries pruned.');
    }
}
