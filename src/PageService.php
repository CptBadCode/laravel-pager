<?php

namespace Cptbadcode\LaravelPager;

use Cptbadcode\LaravelPager\Contracts\IPage;
use Cptbadcode\LaravelPager\Contracts\IPageLoader;
use Cptbadcode\LaravelPager\Contracts\IPageRepository;
use Cptbadcode\LaravelPager\Contracts\Services\IDisableService;

class PageService
{
    const PAGE_NAMESPACE = 'App\\Pages';

    protected IPageLoader $pageLoader;

    protected IDisableService $disableService;

    protected IPageRepository $pageRepository;

    public function __construct(IPageLoader $pageLoader, IDisableService $disableService, IPageRepository $pageRepository)
    {
        $this->pageLoader = $pageLoader;
        $this->disableService = $disableService;
        $this->pageRepository = $pageRepository;
    }

    public function loadPages(): void
    {
        $this->pageLoader->loadPages();
        $loaded = $this->pageLoader->getPages();
        foreach ($loaded as $className) {
            $this->pageRepository->addPage($className);
        }
    }

    public function loadMenu()
    {
        $this->pageLoader->loadMenu();
    }

    public function getMenu(): array
    {
        return $this->pageLoader->getMenu();
    }

    public function repository(): IPageRepository
    {
        return $this->pageRepository;
    }

    public function enablePage(string|IPage ...$pages): void
    {
        array_map(fn($page) => $this->disableService->enable($page), $pages);
    }

    public function disablePage(string|IPage ...$pages): void
    {
        array_map(fn($page) => $this->disableService->disable($page), $pages);
    }

    public function attachMiddleware(array $middleware, string|IPage ...$pages): void
    {
        foreach ($pages as $page) {
            $page = $this->pageRepository->getPageOrFail($page);
            $page->setMiddleware($middleware);
        }
    }

    public function attachMiddlewareAll(array $middleware): void
    {
        foreach ($this->pageRepository->getPages() as $page) {
            $page->setMiddleware($middleware);
        }
    }

    public static function pageLoaderUsing(string $concrete): void
    {
        app()->singleton(IPageLoader::class, $concrete);
    }

    public static function pageDisablerUsing(string $concrete): void
    {
        app()->singleton(IDisableService::class, $concrete);
    }

    public static function pageRepositoryUsing(string $concrete): void
    {
        app()->singleton(IPageRepository::class, $concrete);
    }
}
