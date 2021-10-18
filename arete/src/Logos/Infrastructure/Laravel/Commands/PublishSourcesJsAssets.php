<?php

namespace Arete\Logos\Infrastructure\Laravel\Commands;

use Illuminate\Console\Command;
use Arete\Logos\Application\Ports\Interfaces\CreateSourceUC;

class PublishSourcesJsAssets extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sources:js';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates and publish a js file with logos front-ent data assets';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(CreateSourceUC $sources)
    {
        $sources->publishSourceTypesPresentationScript();
        return 0;
    }
}
