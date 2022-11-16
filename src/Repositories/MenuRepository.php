<?php

namespace Cptbadcode\LaravelPager\Repositories;

use Cptbadcode\LaravelPager\Contracts\IMenuRepository;
use Cptbadcode\LaravelPager\PageService;
use Illuminate\Support\Facades\Cache;

class MenuRepository implements IMenuRepository
{
    protected array $menu = [];

    public function __construct()
    {
        $this->menu = Cache::get(PageService::CACHE_MENU_KEY) ?? [];
    }

    public function getMenu(): array
    {
        return $this->menu;
    }

    public function updateMenu(array $menu): void
    {
        $this->menu = $menu;
        if (PageService::$cacheMenu)
            Cache::forever(PageService::CACHE_MENU_KEY, $this->menu);
    }
}
