<?php

namespace Cptbadcode\LaravelPager\Services;

use Cptbadcode\LaravelPager\Contracts\IPage;
use Cptbadcode\LaravelPager\Contracts\Services\IDisableService;
use Cptbadcode\LaravelPager\PageService;

class DisableService implements IDisableService
{
    /**
     * @param string|IPage $page
     * @return IPage
     */
    public static function enable(string|IPage $page): IPage
    {
        return (PageService::isPage($page))
            ? $page->enable()
            : static::findAndEnable($page);
    }

    /**
     * @param string|IPage $page
     * @return IPage
     */
    public static function disable(string|IPage $page): IPage
    {
        return (PageService::isPage($page))
            ? $page->disable()
            : static::findAndDisable($page);
    }

    /**
     * @param string $key
     * @return IPage
     */
    public static function findAndDisable(string $key): IPage
    {
        $page = PageService::repository()->getPageOrFail($key);
        $page->disable();
        return $page;
    }

    /**
     * @param string $key
     * @return IPage
     */
    public static function findAndEnable(string $key): IPage
    {
        $page = PageService::repository()->getPageOrFail($key);
        $page->enable();
        return $page;
    }
}
