<?php

namespace Cptbadcode\LaravelPager\Actions;

use Cptbadcode\LaravelPager\Contracts\IPage;
use Cptbadcode\LaravelPager\Helpers\MenuGenerator;

abstract class MenuAction
{
    protected function isDir(array $current): bool
    {
        $menuKeys = array_keys(MenuGenerator::getMenuTemplate());
        $actualKeys = array_keys($current);
        return $actualKeys !== $menuKeys;
    }

    abstract protected function doRecursive(array &$menu, IPage ...$pages);
}
