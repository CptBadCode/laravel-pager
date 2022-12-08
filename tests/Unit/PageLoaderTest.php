<?php

namespace Cptbadcode\LaravelPager\Tests\Unit;

use Cptbadcode\LaravelPager\Helpers\PageLoader;
use Cptbadcode\LaravelPager\PageService;
use Cptbadcode\LaravelPager\Tests\PageTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class PageLoaderTest extends PageTestCase
{
    public function test_page_load_is_success()
    {
        $this->assertCount(0, $this->pageRepository->getPages());

        Artisan::call('make:page', ['name' => 'Home']);
        PageLoader::load();

        $this->assertCount(1, $this->pageRepository->getPages());

        Artisan::call('make:page', ['name' => 'About']);
        Artisan::call('make:page', ['name' => 'Catalog']);
        PageLoader::load();

        $this->assertCount(3, $this->pageRepository->getPages());
    }

    public function test_not_load_if_pages_cached()
    {
        $this->assertCount(0, $this->pageRepository->getPages());

        Artisan::call('make:page', ['name' => 'Home']);
        PageLoader::load();

        $this->assertCount(1, $this->pageRepository->getPages());

        Artisan::call('cache:page');

        Artisan::call('make:page', ['name' => 'About']);
        Artisan::call('make:page', ['name' => 'Company']);
        Artisan::call('make:page', ['name' => 'TestPage']);

        $this->assertCount(1, $this->pageRepository->getPages());

        Cache::forget(PageService::CACHE_KEY);
    }
}
