<?php

namespace Cptbadcode\LaravelPager\Tests\Unit;

use Cptbadcode\LaravelPager\Contracts\IPage;
use Cptbadcode\LaravelPager\Helpers\PageLoader;
use Cptbadcode\LaravelPager\PageService;
use Cptbadcode\LaravelPager\Tests\PageTestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class PageRepositoryTest extends PageTestCase
{
    public function test_get_pages()
    {
        Artisan::call('make:page', ['name' => 'Home']);
        PageLoader::load();

        $pages = $this->pageRepository->getPages();
        $this->assertCount(1, $this->pageRepository->getPages());

        $page = PageService::getInstancePages(['home_page'])[0];
        $this->assertEquals($page, $pages['home_page']);
    }

    public function test_get_pages_by_keys()
    {
        Artisan::call('make:page', ['name' => 'Home']);
        Artisan::call('make:page', ['name' => 'About']);
        Artisan::call('make:page', ['name' => 'Dashboard']);

        PageLoader::load();

        $pages = $this->pageRepository->getPagesByKeys('home_page');
        $this->assertCount(1, $pages);

        $pages = $this->pageRepository->getPagesByKeys('home_page', 'dashboard_page');
        $this->assertCount(2, $pages);

        $pages = $this->pageRepository->getPagesByKeys('about_page', 'dashboard_page');
        $this->assertCount(2, $pages);

        $pages = $this->pageRepository->getPagesByKeys('wrong_page', 'wrong_page_2');
        $this->assertCount(0, $pages);
    }

    public function test_cache()
    {
        Artisan::call('make:page', ['name' => 'Home']);
        PageLoader::load();

        $this->pageRepository->cache();

        $this->assertCount(1, $this->pageRepository->getPages());

        $this->assertTrue(Cache::has(PageService::CACHE_KEY));

        Cache::forget(PageService::CACHE_KEY);
    }

    public function test_get_page()
    {
        Artisan::call('make:page', ['name' => 'Home']);
        Artisan::call('make:page', ['name' => 'About']);
        PageLoader::load();

        $page = $this->pageRepository->getPage('home_page');
        $this->assertInstanceOf(IPage::class, $page);
        $this->assertEquals('home_page', $page->getKey());

        $page = $this->pageRepository->getPage('about_page');
        $this->assertInstanceOf(IPage::class, $page);
        $this->assertEquals('about_page', $page->getKey());

        $page = $this->pageRepository->getPage('wrong_page');
        $this->assertNull($page);
    }

    public function test_clear()
    {
        Artisan::call('make:page', ['name' => 'Home']);
        PageLoader::load();

        Artisan::call('cache:page');

        $this->assertCount(1, $this->pageRepository->getPages());
        $this->assertTrue(Cache::has(PageService::CACHE_KEY));
        $this->assertCount(1, Cache::get(PageService::CACHE_KEY));

        $this->pageRepository->clear();

        $this->assertNotTrue(Cache::has(PageService::CACHE_KEY));
        $this->assertCount(0, $this->pageRepository->getPages());
        $this->assertEquals(0, Cache::get(PageService::CACHE_KEY));
    }

    public function test_add_pages()
    {
        Artisan::call('make:page', ['name' => 'Home']);
        Artisan::call('make:page', ['name' => 'About']);

        $this->pageRepository->addPages([
            PageService::PAGE_NAMESPACE.'\HomePage',
            PageService::PAGE_NAMESPACE.'\AboutPage'
        ]);

        $this->assertCount(2, $this->pageRepository->getPages());

        $this->pageRepository->clear();

        $this->pageRepository->addPages([
            'home_page',
            'about_page'
        ]);

        $this->assertCount(0, $this->pageRepository->getPages());
    }

    public function test_add_page()
    {
        Artisan::call('make:page', ['name' => 'Home']);
        Artisan::call('make:page', ['name' => 'About']);

        $this->pageRepository->addPage(PageService::PAGE_NAMESPACE.'\HomePage');
        $this->assertCount(1, $this->pageRepository->getPages());

        $this->assertTrue($this->pageRepository->addPage(PageService::PAGE_NAMESPACE.'\AboutPage'));
        $this->assertCount(2, $this->pageRepository->getPages());

        $this->assertFalse($this->pageRepository->addPage(PageService::PAGE_NAMESPACE.'\WrongPage'));
        $this->assertFalse($this->pageRepository->addPage('home_page'));
    }

    public function test_get_page_or_fail()
    {
        Artisan::call('make:page', ['name' => 'Home']);
        PageLoader::load();

        $page = $this->pageRepository->getPageOrFail('home_page');
        $this->assertInstanceOf(IPage::class, $page);
        $this->assertEquals('home_page', $page->getKey());

        $this->assertThrows(fn() => $this->pageRepository->getPageOrFail('about_page'),
            \RuntimeException::class,
            "Page about_page not found"
        );
    }
}
