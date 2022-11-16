<?php

namespace Cptbadcode\LaravelPager;

use Cptbadcode\LaravelPager\Helpers\MenuLoader;
use Cptbadcode\LaravelPager\Helpers\PageLoader;
use Cptbadcode\LaravelPager\Services\DisableService;
use Cptbadcode\LaravelPager\Contracts\{IMenuRepository, IPage, IPageRepository};

class PageService
{
    const PAGE_NAMESPACE = 'App\\Pages',
          CACHE_PAGE_KEY = 'pages',
          CACHE_MENU_KEY = 'menu';

    public static bool
        $cachedPage = false,
        $cacheMenu = false;

    public static function loadPages(): void
    {
        PageLoader::load();
    }

    public static function loadMenu(): void
    {
        MenuLoader::load();
    }

    public static function repository(): IPageRepository
    {
        return app(IPageRepository::class);
    }

    public static function menu(): IMenuRepository
    {
        return app(IMenuRepository::class);
    }

    public static function enablePage(string|IPage ...$pages): void
    {
        array_map(fn($page) => DisableService::enable($page), $pages);
    }

    public static function disablePage(string|IPage ...$pages): void
    {
        array_map(fn($page) => DisableService::disable($page), $pages);
    }

    public static function applyMiddleware(array $middleware, string|IPage ...$pages): void
    {
        foreach ($pages as $page) {
            $page = static::repository()->getPageOrFail($page);
            $page->setMiddleware($middleware);
        }
    }

    public static function applyMiddlewareAll(array $middleware): void
    {
        foreach (static::repository()->getPages() as $page) {
            $page->setMiddleware($middleware);
        }
    }

    public static function pageRepositoryUsing(string $concrete): void
    {
        app()->singleton(IPageRepository::class, $concrete);
    }

    public static function menuRepositoryUsing(string $concrete): void
    {
        app()->singleton(IMenuRepository::class, $concrete);
    }

    public static function getRootPath(): string
    {
        return base_path(self::PAGE_NAMESPACE);
    }

    public static function enableCacheMenu(): static
    {
        self::$cacheMenu = true;

        return new static;
    }

    public static function enableCachePage(): static
    {
        self::$cachedPage = true;

        return new static;
    }
}
