<?php

namespace Cptbadcode\LaravelPager\Tests;

use Cptbadcode\LaravelPager\Helpers\PageLoader;
use Cptbadcode\LaravelPager\PageService;
use Tests\TestCase;

class PageLoaderTest extends TestCase
{
    public function test_load_is_success()
    {
        $repository = PageService::repository();

        PageLoader::load();
    }
}
