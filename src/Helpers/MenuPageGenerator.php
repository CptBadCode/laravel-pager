<?php

namespace Cptbadcode\LaravelPager\Helpers;

use Cptbadcode\LaravelPager\Contracts\Menu\{IMenuDirectory, IMenu};
use Cptbadcode\LaravelPager\Menu\{MenuDirectory, Menu};

class MenuPageGenerator
{
    /**
     * @param array $pages
     * @return IMenu
     */
    public static function generateMenu(array $pages): IMenu
    {
        $menu = new Menu([]);
        foreach ($pages as $k => $page) {
            if (is_array($page)) $menu->add(self::doRecursive($k, $page));
            else $menu->add($page->forMenu());
        }

        return $menu;
    }

    private static function doRecursive(string $dirTitle, array $pages): IMenuDirectory
    {
        $directory = new MenuDirectory($dirTitle, $dirTitle);
        $items = [];
        foreach ($pages as $k => $page) {
            if (is_array($page)) $items[] = self::doRecursive($k, ...$page);
            else $items[] = $page->forMenu();
        }

        $directory->addItems(...$items);
        return $directory;
    }
}
