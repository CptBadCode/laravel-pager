<?php

namespace Cptbadcode\LaravelPager\Console\Commands;

use Cptbadcode\LaravelPager\Services\MenuService;
use \Illuminate\Console\Command;

class CacheMenuCommand extends Command
{
    protected $signature = 'cache:menu';

    protected $description = 'Cache menu';

    public function handle()
    {
        MenuService::repository()->cache();

        $this->info('menu cached success');

        return Command::SUCCESS;
    }
}
