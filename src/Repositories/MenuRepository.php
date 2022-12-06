<?php

namespace Cptbadcode\LaravelPager\Repositories;

use Cptbadcode\LaravelPager\Contracts\IRepository;
use Cptbadcode\LaravelPager\Services\MenuService;
use Cptbadcode\LaravelPager\Contracts\Menu\{IMenu, IMenuRepository};
use Illuminate\Support\Facades\Cache;

class MenuRepository implements IMenuRepository, IRepository
{
    protected array $menu = [];

    public function __construct()
    {
        $this->menu = Cache::get(MenuService::CACHE_KEY) ?? [];
    }

    /**
     * @return array|null
     */
    public function getMenu(): ?array
    {
        return $this->find(MenuService::BASE_MENU_KEY)?->getMenu();
    }

    /**
     * @param string $menuName
     * @return IMenu|null
     *
     */
    public function find(string $menuName): ?IMenu
    {
        return $this->menu[$menuName] ?? null;
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->menu;
    }

    /**
     * @param string $menuName
     * @param IMenu $menu
     * @return IMenu
     */
    public function addOrUpdate(string $menuName, IMenu $menu): IMenu
    {
        $this->menu[$menuName] = $menu;

        return $menu;
    }

    /**
     * @param string $menuName
     * @return void
     */
    public function sort(string $menuName)
    {
        $menu = $this->find($menuName);
        if ($menu) {
            $menu->sort();
            $this->addOrUpdate($menuName, $menu);
        }
    }

    public function cache(): void
    {
        Cache::forever(MenuService::CACHE_KEY, $this->menu);
    }

    public function clear()
    {
        $this->menu = [];
        Cache::forget(MenuService::CACHE_KEY);
    }
}
