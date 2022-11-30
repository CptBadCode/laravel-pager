<?php

namespace Cptbadcode\LaravelPager\Repositories;

use Cptbadcode\LaravelPager\Menu\Menu;
use Cptbadcode\LaravelPager\Services\MenuService;
use Cptbadcode\LaravelPager\Contracts\{IMenu, IMenuRepository};
use Illuminate\Support\Facades\Cache;

class MenuRepository implements IMenuRepository
{
    protected array $menu = [];

    public function __construct()
    {
        $this->menu = Cache::get(MenuService::CACHE_MENU_KEY) ?? [];
    }

    /**
     * @return IMenu|null
     */
    public function getMenu(): ?IMenu
    {
        return $this->find(MenuService::BASE_MENU_KEY);
    }

    /**
     * @param string $key
     * @return IMenu|null
     *
     */
    public function find(string $key): ?IMenu
    {
        return $this->menu[$key] ?? null;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->menu;
    }

    /**
     * @param string $key
     * @param IMenu $menu
     * @return IMenu
     */
    public function addOrUpdate(string $key, IMenu $menu): IMenu
    {
        $this->menu[$key] = $menu;
        if (MenuService::$cacheMenu)
            Cache::forever(MenuService::CACHE_MENU_KEY, $this->menu);

        return $menu;
    }
}
