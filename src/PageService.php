<?php

namespace Cptbadcode\LaravelPager;

use Cptbadcode\LaravelPager\Helpers\{MenuLoader, PageLoader};
use Cptbadcode\LaravelPager\Services\DisableService;
use Cptbadcode\LaravelPager\Contracts\{IMenuRemover, IMenuRepository, IMenuUpdater, IPage, IPageRepository};

class PageService
{
    const
        PAGE_NAMESPACE = 'App\\Pages',
        LANG_FILE = 'page',
        CACHE_PAGE_KEY = 'pages',
        CACHE_MENU_KEY = 'menu',
        ROOT_VIEW = 'laravel-pager::app',
        DEFAULT_HEADER_COMPONENT = 'laravel-pager::components.layouts.header',
        DEFAULT_BODY_COMPONENT = 'laravel-pager::components.layouts.body',
        DEFAULT_FOOTER_COMPONENT = 'laravel-pager::components.layouts.footer';

    public static bool
        $cachedPage = false,
        $cacheMenu = false;

    public static array
        $globalComponents = [];

    protected static array
        $headerComponents = [],
        $footerComponents = [];


    public static function getRootPath(): string
    {
        return base_path(self::PAGE_NAMESPACE);
    }

    /**
     * Load all page from filesystem
     * @note If you enable cache. Clear this
     * @return void
     */
    public static function loadPages(): void
    {
        PageLoader::load();
    }

    /**
     * Load menu from page filesystem
     * @note If you enable cache. Clear this
     * @return void
     */
    public static function loadMenu(): void
    {
        MenuLoader::load();
    }

    /**
     * get page repository
     * @return IPageRepository
     */
    public static function repository(): IPageRepository
    {
        return app(IPageRepository::class);
    }

    /**
     * get menu repository
     * @return IMenuRepository
     */
    public static function menu(): IMenuRepository
    {
        return app(IMenuRepository::class);
    }

    public static function removeFromMenu(string|IPage ...$pages): void
    {
        $pages = self::convertKeysToPage(...$pages);
        self::menu()->removeFromMenu(...$pages);
    }

    /**
     * Enable pages
     * @param string|IPage ...$pages
     * @return void
     */
    public static function enablePage(string|IPage ...$pages): void
    {
        foreach ($pages as $page) {
            DisableService::enable($page);
        }

        self::menu()->updateMenu();
    }

    /**
     * Disable pages
     * @param string|IPage ...$pages
     * @return void
     */
    public static function disablePage(string|IPage ...$pages): void
    {
        foreach ($pages as $page) {
            DisableService::disable($page);
        }

        self::menu()->updateMenu();
    }

    /**
     * @param array $middleware
     * @param string|IPage ...$pages
     * @return void
     */
    public static function applyMiddleware(array $middleware, string|IPage ...$pages): void
    {
        foreach ($pages as $page) {
            $page = static::repository()->getPageOrFail($page);
            $page->setMiddleware($middleware);
        }
    }

    /**
     * @param array $middleware
     * @return void
     */
    public static function applyMiddlewareAll(array $middleware): void
    {
        foreach (static::repository()->getPages() as $page) {
            $page->setMiddleware($middleware);
        }
    }

    /**
     * class is page object
     * @param string $className
     * @return bool
     */
    public static function isPage(string $className): bool
    {
        return class_exists($className) && is_subclass_of($className, BasePage::class);
    }

    /**
     * @return static
     */
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

    /**
     *
     * @param string $tag
     * @return static
     */
    public static function addGlobalComponent(string $tag): static
    {
        self::$globalComponents[] = $tag;

        return new static;
    }

    /**
     * pages using header
     * @param string $componentPath
     * @param string ...$pageKeys
     * @return static
     */
    public static function headerComponentUsing(string $componentPath, string ...$pageKeys): static
    {
        self::$headerComponents = array_fill_keys($pageKeys, $componentPath);

        return new static;
    }

    /**
     * pages using footer
     * @param string $componentPath
     * @param string ...$pageKeys
     * @return static
     */
    public static function footerComponentUsing(string $componentPath, string ...$pageKeys): static
    {
        self::$footerComponents = array_fill_keys($pageKeys, $componentPath);

        return new static;
    }

    public static function headerForPage(string $pageKey)
    {
        return self::$headerComponents[$pageKey] ?? null;
    }

    public static function footerForPage(string $pageKey)
    {
        return self::$footerComponents[$pageKey] ?? null;
    }

    public static function pageRepositoryUsing(string $concrete): void
    {
        app()->singleton(IPageRepository::class, $concrete);
    }

    public static function menuRepositoryUsing(string $concrete): void
    {
        app()->singleton(IMenuRepository::class, $concrete);
    }

    public static function menuUpdaterUsing(string $concrete): void
    {
        app()->singleton(IMenuUpdater::class, $concrete);
    }

    public static function menuRemoverUsing(string $concrete): void
    {
        app()->singleton(IMenuRemover::class, $concrete);
    }

    private static function convertKeysToPage(string|IPage ...$pages)
    {
        return array_reduce($pages, function ($res, $page) {
            $res[] = (is_string($page)) ? self::repository()->getPageOrFail($page) : $page;
            return $res;
        }, []);
    }
}
