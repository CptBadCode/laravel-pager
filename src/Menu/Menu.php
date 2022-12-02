<?php

namespace Cptbadcode\LaravelPager\Menu;

use Cptbadcode\LaravelPager\Contracts\IPage;
use Cptbadcode\LaravelPager\Helpers\MenuGenerator;
use Cptbadcode\LaravelPager\Helpers\MenuSorter;
use Cptbadcode\LaravelPager\Services\MenuService;
use Cptbadcode\LaravelPager\Contracts\Menu\{IMenu, IMenuRemover, IMenuUpdater, IMenuDirectory, IMenuItem};

class Menu implements IMenu
{
    protected array $menu = [];

    protected bool $inline = false;

    protected IMenuUpdater $menuUpdater;

    protected IMenuRemover $menuRemover;

    public function __construct(array $menu)
    {
        $this->menuUpdater = app(IMenuUpdater::class);
        $this->menuRemover = app(IMenuRemover::class);
        $this->setMenu($menu);
    }

    /**
     * @param array $menu
     * @return void
     */
    public function setMenu(array $menu): void
    {
        $this->menu = $menu;
    }

    /**
     * @return array
     */
    public function getMenu(): array
    {
        return $this->menu;
    }

    /**
     * @param IMenuDirectory|IMenuItem $item
     * @return void
     */
    public function add(IMenuDirectory|IMenuItem $item)
    {
        $this->menu[] = $item;
    }

    /**
     * Обновить страницу в меню
     * @param IPage ...$pages
     * @return void
     */
    public function updateMenuByPages(IPage ...$pages): void
    {
        $this->setMenu($this->menuUpdater->updateByPages($this->menu, ...$pages));
    }

    /**
     * Обновить меню
     * @return void
     */
    public function updateMenu(): void
    {
        $this->setMenu(
            $this->menuUpdater->updateAll($this->menu)
        );
    }

    /**
     * Удалить страницы из меню
     * @param IPage ...$pages
     * @return void
     */
    public function removeFromMenu(IPage ...$pages): void
    {
        $this->setMenu($this->menuRemover->remove($this->menu, ...$pages));
    }

    /**
     * Рекурсивный поиск каталога по ключу
     * @param string $key
     * @return IMenuDirectory|IMenuItem|null
     * @see MenuGenerator
     */
    public function find(string $key): IMenuDirectory|IMenuItem|null
    {
        return MenuService::menuItemsHasKey($key, ...$this->menu);
    }

    public function sort()
    {
        $this->menu = MenuSorter::sort(...$this->menu);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_reduce($this->menu, function ($res, $menu) {
            $res[$menu->key ?? $menu->title] = $menu->toArray();
            return $res;
        });
    }
}
