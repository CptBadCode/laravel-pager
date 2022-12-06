<?php

namespace Cptbadcode\LaravelPager;

use Cptbadcode\LaravelPager\Services\MenuService;
use Cptbadcode\LaravelPager\Helpers\PageLoader;
use Cptbadcode\LaravelPager\Services\DisableService;
use Cptbadcode\LaravelPager\Traits\Caching;
use Cptbadcode\LaravelPager\Contracts\{IPage, IPageRepository};

class PageService
{
    use Caching;

    const
        PAGE_NAMESPACE = 'App\\Pages',
        LANG_FILE = 'page',
        CACHE_KEY = 'pages',
        ROOT_VIEW = 'laravel-pager::app',
        DEFAULT_TEMPLATE = 'laravel-pager::components.templates.main',
        DEFAULT_HEADER = 'layouts.header',
        DEFAULT_BODY = 'layouts.body',
        DEFAULT_FOOTER = 'layouts.footer';

    public static bool
        $cached = false,
        $localeTitle = false;

    public static array
        $dynamicComponents = [];

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
     * get page repository
     * @return IPageRepository
     */
    public static function repository(): IPageRepository
    {
        return app(IPageRepository::class);
    }

    /**
     * @param string $key
     * @param string|IPage ...$pages
     * @return void
     */
    public static function removeFromMenu(string $key, string|IPage ...$pages): void
    {
        $pages = self::getInstancePages($pages);
        MenuService::repository()
            ->find($key)
            ->removeFromMenu(...$pages);
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

        MenuService::updateMenuWhereHas(...$pages);
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

        MenuService::updateMenuWhereHas(...$pages);
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
            $page->addMiddleware($middleware);
        }
    }

    /**
     * @param array $middleware
     * @return void
     */
    public static function applyMiddlewareAll(array $middleware): void
    {
        foreach (static::repository()->getPages() as $page) {
            $page->addMiddleware($middleware);
        }
    }

    /**
     * class is page object
     * @param string|IPage $class
     * @return bool
     */
    public static function isPage(string|IPage $class): bool
    {
        return (class_exists($class) && is_subclass_of($class, BasePage::class)) || $class instanceof IPage;
    }

    public static function enableLocaleTitle(): static
    {
        self::$localeTitle = true;

        return new static;
    }

    /**
     *
     * @param string $tag
     * @return static
     */
    public static function addDynamicComponent(string $tag): static
    {
        self::$dynamicComponents[] = $tag;

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

    /**
     * @param string $templatePath
     * @param IPage|string ...$pages
     * @return void
     */
    public static function useTemplateForPage(string $templatePath, IPage|string ...$pages): void
    {
        foreach (self::getInstancePages($pages) as $page) {
            $page->useTemplate($templatePath);
        }
    }

    public static function pageRepositoryUsing(string $concrete): void
    {
        app()->singleton(IPageRepository::class, $concrete);
    }

    /**
     * Получить объекты страниц из переданных ключей
     * @param array $pages
     * @return array
     */
    public static function getInstancePages(array $pages): array
    {
        $result = [];
        foreach ($pages as $k => $page) {
            if (is_array($page)) $result[$k] = self::getInstancePages($page);
            else $result[$k] = self::repository()->getPageOrFail(is_string($page) ? $page : $page::$key);
        }
        return $result;
    }
}
