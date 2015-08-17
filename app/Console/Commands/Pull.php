<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Support\ProviderManager;

class Pull extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hjem:pull';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pulls data from applicable providers';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        ProviderManager::pull();
    }
}
