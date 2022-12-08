<?php

namespace Cptbadcode\LaravelPager\Tests;

use Cptbadcode\LaravelPager\Contracts\IPageRepository;
use Cptbadcode\LaravelPager\Helpers\PageLoader;
use Cptbadcode\LaravelPager\PageService;
use Tests\TestCase;

abstract class PageTestCase extends TestCase
{
    use RefreshPages;

    protected ?IPageRepository $pageRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->clearDirPage();

        PageLoader::load();
        $this->pageRepository = PageService::repository();
    }

    protected function tearDown(): void
    {
        $this->clearDirPage();

        $this->pageRepository = null;
        parent::tearDown();
    }
}
