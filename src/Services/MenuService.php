<?php

namespace Cptbadcode\LaravelPager\Services;

use Cptbadcode\LaravelPager\Contracts\{
    IMenuUpdater,
    IMenuRepository,
    IMenuRemover,
    IPage,
    Menu\IMenuDirectory,
    Menu\IMenuItem
};
use Cptbadcode\LaravelPager\Helpers\MenuLoader;

class MenuService
{
    const CACHE_MENU_KEY = 'menu';
    const BASE_MENU_KEY = 'main';

    public static bool
        $cacheMenu = false;

    /**
     * Load menu from page filesystem
     * @note If you enable cache. Clear this
     * @return void
     */
    public static function loadMenu(): void
    {
        MenuLoader::loadDefault();
    }

    /**
     * load addition menu from dir
     * @param string $nameMenu
     * @param string $filepath
     * @return void
     */
    public static function generateMenu(string $nameMenu, string $filepath): void
    {
        MenuLoader::load($nameMenu, $filepath);
    }

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

    /**
     * Включить кеш меню
     * @return static
     */
    public static function enableCacheMenu(): static
    {
        self::$cacheMenu = true;

        return new static;
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
