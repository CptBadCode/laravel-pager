<?php

namespace Cptbadcode\LaravelPager\Tests;

use Cptbadcode\LaravelPager\PageService;
use Illuminate\Support\Facades\File;

trait RefreshPages
{
    private function clearDirPage(): void
    {
        if (File::exists(PageService::getRootPath()))
            File::deleteDirectory(PageService::getRootPath());
    }
}
