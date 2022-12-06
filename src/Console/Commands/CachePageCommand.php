<?php

namespace Cptbadcode\LaravelPager\Console\Commands;

use Cptbadcode\LaravelPager\PageService;
use \Illuminate\Console\Command;

class CachePageCommand extends Command
{
    protected $signature = 'cache:page';

    protected $description = 'Cache pages';

    public function handle()
    {
        PageService::repository()->cache();

        $this->info('pages cached success');

        return Command::SUCCESS;
    }
}
