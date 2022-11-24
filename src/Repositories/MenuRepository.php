<?php

namespace Cptbadcode\LaravelPager\Repositories;

use Cptbadcode\LaravelPager\Contracts\{IMenuRemover, IMenuUpdater, IPage, IMenuRepository};
use Cptbadcode\LaravelPager\PageService;
use Illuminate\Support\Facades\Cache;

class MenuRepository implements IMenuRepository
{
    protected array $menu = [];

    protected IMenuUpdater $menuUpdater;

    protected IMenuRemover $menuRemover;

    public function __construct(IMenuUpdater $menuUpdater, IMenuRemover $menuRemover)
    {
        $this->menu = Cache::get(PageService::CACHE_MENU_KEY) ?? [];

        $this->menuUpdater = $menuUpdater;
        $this->menuRemover = $menuRemover;
    }

    public function getMenu(): array
    {
        return $this->menu;
    }

    public function setMenu(array $menu): void
    {
        $this->menu = $menu;
        if (PageService::$cacheMenu)
            Cache::forever(PageService::CACHE_MENU_KEY, $this->menu);
    }

    public function updateMenuByPage(IPage $page)
    {
        $this->setMenu($this->menuUpdater->updateByPage($this->menu, $page));
    }

    public function updateMenu()
    {
        $this->setMenu(
            $this->menuUpdater->updateAll($this->menu)
        );
    }

    public function removeFromMenu(IPage ...$pages)
    {
        $this->setMenu($this->menuRemover->remove($this->menu, ...$pages));
    }
}
