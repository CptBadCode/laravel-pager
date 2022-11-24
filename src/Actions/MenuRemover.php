<?php

namespace Cptbadcode\LaravelPager\Actions;

use Cptbadcode\LaravelPager\Contracts\{IPage, IMenuRemover};

class MenuRemover extends MenuAction implements IMenuRemover
{
    public function remove(array $menu, IPage ...$pages): array
    {
        $this->doRecursive($menu, ...$pages);
        return $menu;
    }

    protected function doRecursive(array &$menu, IPage ...$pages): void
    {
        foreach ($menu as $pageKey => &$item) {
            if (array_some(fn($page) => $page->getKey() === $pageKey, $pages)) unset($menu[$pageKey]);
            else if ($this->isDir($item))  {
                $this->doRecursive($item, ...$pages);
                if (!count($item)) unset($menu[$pageKey]);
            }
        }
    }
}
