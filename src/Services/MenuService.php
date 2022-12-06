<?php

namespace Cptbadcode\LaravelPager\Services;

use Cptbadcode\LaravelPager\Contracts\IPage;
use Cptbadcode\LaravelPager\Contracts\Menu\{
    IMenuUpdater,
    IMenuRepository,
    IMenuRemover,
    IMenuDirectory,
    IMenuItem
};
use Cptbadcode\LaravelPager\Helpers\MenuLoader;

class MenuService
{
    const CACHE_KEY = 'menu';
    const BASE_MENU_KEY = 'main';

    /**
     * Load menu from page filesystem
     * @note If you enable cache. Clear this
     * @param array $attributes
     * @return void
     */
    public static function loadMenu(array $attributes = []): void
    {
        MenuLoader::loadDefault($attributes);
    }

    /**
     * load addition menu from dir
     * @param string $nameMenu
     * @param string $filepath
     * @param array $attributes
     * @return void
     */
    public static function generateMenu(string $nameMenu, string $filepath, array $attributes = []): void
    {
        MenuLoader::load($nameMenu, $filepath, $attributes);
    }

    /**
     * load menu from pages names
     * @param string $nameMenu
     * @param array $pages
     * @return void
     */
    public static function generateMenuFromPages(string $nameMenu, array $pages): void
    {
        MenuLoader::loadFromPages($nameMenu, $pages);
    }

    /**
     * @param string $key
     * @param IMenuDirectory|IMenuItem ...$items
     * @return IMenuDirectory|IMenuItem|null
     */
    public static function menuItemsHasKey(string $key, IMenuDirectory|IMenuItem ...$items): IMenuDirectory|IMenuItem|null
    {
        foreach ($items as $item) {
            $dir = $item->find($key);
            if ($dir) return $dir;
        }
        return null;
    }

    /**
     * Обновить все меню где есть указанные страницы
     * новыми данными
     * @param IPage ...$pages
     * @return void
     */
    public static function updateMenuWhereHas(IPage ...$pages): void
    {
        collect(self::repository()->getAll())->each(
            function($menu, $k) use ($pages) {
                $menu->updateMenuByPages(...$pages);
                self::repository()->addOrUpdate($k, $menu);
            }
        );
    }

    public static function removeFromMenuWhereHas(IPage ...$pages): void
    {
        collect(self::repository()->getAll())->each(fn($menu) => $menu->removeFromMenu(...$pages));
    }

    /**
     * Соответствует ли переданные страницы каталогу или элементу меню
     * @param IMenuDirectory|IMenuItem $item
     * @param IPage ...$pages
     * @return bool
     */
    public static function pagesExistsInMenu(IMenuDirectory|IMenuItem $item, IPage ...$pages): bool
    {
        return array_some(fn($page) => $page->getKey() === $item->key, $pages);
    }

    /**
     * @return IMenuRepository
     */
    public static function repository(): IMenuRepository
    {
        return app(IMenuRepository::class);
    }

    public static function isDir($item): bool
    {
        return $item instanceof IMenuDirectory;
    }

    public static function menuRepositoryUsing(string $concrete): void
    {
        app()->singleton(IMenuRepository::class, $concrete);
    }

    public static function menuUpdaterUsing(string $concrete): void
    {
        app()->singleton(IMenuUpdater::class, $concrete);
    }

    public static function menuRemoverUsing(string $concrete): void
    {
        app()->singleton(IMenuRemover::class, $concrete);
    }
}
