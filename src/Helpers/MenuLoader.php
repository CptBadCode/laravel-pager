<?php

namespace Cptbadcode\LaravelPager\Helpers;

use Cptbadcode\LaravelPager\Services\MenuService;
use Illuminate\Support\Facades\Cache;
use Cptbadcode\LaravelPager\PageService;
use Cptbadcode\LaravelPager\Contracts\IMenuLoader;

class MenuLoader implements IMenuLoader
{
    public static function loadDefault(array $attributes = [])
    {
        self::load(MenuService::BASE_MENU_KEY, PageService::getRootPath(), $attributes);
    }

    public static function load(string $nameMenu, string $filepath, array $attributes = [])
    {
        if (!Cache::has(MenuService::CACHE_MENU_KEY)) {
            MenuService::repository()
                ->addOrUpdate(
                    $nameMenu,
                    MenuGenerator::generateMenu($filepath, $attributes)
                );
        }
        MenuService::repository()->sort($nameMenu);
    }
}
