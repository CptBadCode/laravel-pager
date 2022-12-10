<?php

namespace Cptbadcode\LaravelPager\Console\Commands;

use Cptbadcode\LaravelPager\PageService;
use \Illuminate\Console\Command;

class CreatePageCommand extends Command
{
    use CommandFileCreator;

    protected $signature = 'make:page {name : Name handle page} {--title= : Title Page} {--base_dir= : Dir to upload}';

    protected $description = 'Create new page';

    protected string $stubPath = 'page.stub';

    protected string $fileNamespace = PageService::PAGE_NAMESPACE;

    protected string $prefix = 'Page';
}
