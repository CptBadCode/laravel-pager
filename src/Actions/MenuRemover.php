<?php

namespace Cptbadcode\LaravelPager\Actions;

use Cptbadcode\LaravelPager\Services\MenuService;
use Cptbadcode\LaravelPager\Contracts\{IPage, IMenuRemover, Menu\IMenuDirectory, Menu\IMenuItem};

class MenuRemover implements IMenuRemover
{
    /**
     * @param array $menu
     * @param IPage ...$pages
     * @return array
     */
    public function remove(array $menu, IPage ...$pages): array
    {
        $menu = array_filter($menu, function ($item) use ($pages) {
            $find = MenuService::pagesExistsInMenu($item, ...$pages);
            if ($find) return false;
            else if (MenuService::isDir($item)) {
                $this->removeRecursive($item, ...$pages);
                if ($item->isEmpty()) return false;
            }
            return true;
        });
        return array_values($menu);
    }

    /**
     * @param IMenuDirectory|IMenuItem $item
     * @param IPage ...$pages
     * @return void
     */
    protected function removeRecursive(IMenuDirectory|IMenuItem $item, IPage ...$pages): void
    {
        foreach ($item->getItems() as $current) {
            if (MenuService::isDir($current)) {
                $this->removeRecursive($current, ...$pages);
                if ($current->isEmpty()) $item->remove($current);
            }
            else {
                $find = MenuService::pagesExistsInMenu($current, ...$pages);
                if ($find) $item->remove($current);
            }
        }
    }
}
