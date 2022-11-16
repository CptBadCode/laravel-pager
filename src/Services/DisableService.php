<?php

namespace Cptbadcode\LaravelPager\Services;

use Cptbadcode\LaravelPager\Contracts\IPage;
use Cptbadcode\LaravelPager\Contracts\Services\IDisableService;
use Cptbadcode\LaravelPager\PageService;

class DisableService implements IDisableService
{
    public static function enable(string|IPage $page): void
    {
        ($page instanceof IPage)
            ? $page->enable()
            : static::findAndEnable($page);
    }

    public static function disable(string|Ipage $page): void
    {
        ($page instanceof IPage)
            ? $page->disable()
            : static::findAndDisable($page);
    }

    public static function findAndDisable(string $key): void
    {
        $page = PageService::repository()->getPageOrFail($key);
        $page->disable();
    }

    public static function findAndEnable(string $key): void
    {
        $page = PageService::repository()->getPageOrFail($key);
        $page->enable();
    }
}
