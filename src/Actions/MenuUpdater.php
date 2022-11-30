<?php

namespace Cptbadcode\LaravelPager\Actions;

use Cptbadcode\LaravelPager\Contracts\{IPage, IMenuUpdater};;
use Cptbadcode\LaravelPager\Contracts\Menu\IMenuDirectory;
use Cptbadcode\LaravelPager\PageService;
use Cptbadcode\LaravelPager\Services\MenuService;

class MenuUpdater implements IMenuUpdater
{
    /**
     * @param array $menu
     * @return array
     */
    public function updateAll(array $menu): array
    {
        return $this->updateByPages($menu, ...PageService::repository()->getPages());
    }

    /**
     * @param array $menu
     * @param IPage ...$pages
     * @return array
     */
    public function updateByPages(array $menu, IPage ...$pages): array
    {
        foreach ($pages as $page) {
            $menu = $this->updateByPage($menu, $page);
        }

        return $menu;
    }

    /**
     * @param array $menu
     * @param IPage $page
     * @return array
     */
    public function updateByPage(array $menu, IPage $page): array
    {
        foreach ($menu as $k => $item) {
            $find = MenuService::pagesExistsInMenu($item, $page);
            if ($find) $menu[$k] = $page->forMenu();
            else if ($item instanceof IMenuDirectory) {
                $menu[$k] = $this->updateRecursive($item, $page);
            }
        }
        return $menu;
    }

    /**
     * @param IMenuDirectory $item
     * @param IPage $page
     * @return IMenuDirectory
     */
    protected function updateRecursive(IMenuDirectory $item, IPage $page): IMenuDirectory
    {
        foreach ($item->getItems() as $k => $current) {
            if ($current instanceof IMenuDirectory)
                $item->update($k, $this->updateRecursive($current, $page));
            else if (MenuService::pagesExistsInMenu($current, $page)) {
                $item->update($k, $page->forMenu());
            }
        }
        return $item;
    }
}
