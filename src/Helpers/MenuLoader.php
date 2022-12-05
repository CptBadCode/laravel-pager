<?php

namespace Cptbadcode\LaravelPager\Helpers;

use Cptbadcode\LaravelPager\Contracts\Menu\IMenu;
use Cptbadcode\LaravelPager\Services\MenuService;
use Illuminate\Support\Facades\Cache;
use Cptbadcode\LaravelPager\PageService;
use Cptbadcode\LaravelPager\Contracts\Menu\IMenuLoader;


class MenuLoader implements IMenuLoader
{
    public static function loadDefault(array $attributes = [])
    {
        self::load(MenuService::BASE_MENU_KEY, PageService::getRootPath(), $attributes);
    }

    public static function load(string $nameMenu, string $filepath, array $attributes = [])
    {
        self::apply($nameMenu, MenuGenerator::generateMenu($filepath, $attributes));
        MenuService::repository()->sort($nameMenu);
    }

    /**
     * @param string $nameMenu
     * @param array $pages
     * @return void
     */
    public static function loadFromPages(string $nameMenu, array $pages): void
    {
        self::apply(
            $nameMenu,
            MenuPageGenerator::generateMenu(
                PageService::getInstancePages($pages)
            ));
    }

    private static function apply(string $nameMenu, IMenu $loaded)
    {
        if (!Cache::has(MenuService::CACHE_KEY)) {
            MenuService::repository()
                ->addOrUpdate(
                    $nameMenu,
                    $loaded
                );
        }
    }
}
