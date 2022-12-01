<?php

namespace Cptbadcode\LaravelPager\Helpers;

use Cptbadcode\LaravelPager\Contracts\Menu\IMenuDirectory;
use Cptbadcode\LaravelPager\Contracts\Menu\IMenuItem;
use Cptbadcode\LaravelPager\Services\MenuService;

class MenuSorter
{
    public static function sort(IMenuDirectory|IMenuItem ...$items): array
    {
        usort($items, function ($first, $second) {
            if (MenuService::isDir($first)) $first->sortItems();
            if (MenuService::isDir($second)) $second->sortItems();
            if ($first->sortKey === $second->sortKey) return 0;
            return $first->sortKey < $second->sortKey ? -1 : 1;
        });

        return $items;
    }
}
