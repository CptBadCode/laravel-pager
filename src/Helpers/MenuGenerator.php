<?php

namespace Cptbadcode\LaravelPager\Helpers;

use Cptbadcode\LaravelPager\Contracts\IMenu;
use Cptbadcode\LaravelPager\Contracts\Menu\{IMenuItem, IMenuDirectory};
use Cptbadcode\LaravelPager\Menu\{Menu, MenuDirectory};
use Cptbadcode\LaravelPager\PageService;

class MenuGenerator
{
    public static function generateMenu(string $path): Menu
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        $menu = new Menu([]);
        foreach ($iterator as $splFileInfo) {
            if ($iterator->isDot()) continue;

            if (!$path = self::getMenuObject($splFileInfo)) continue;

            $path = self::reduceInDepth($path, $iterator, $menu);

            if(!$menu->find($path->key)) $menu->add($path);
        }
        return $menu;
    }

    /**
     * Получить директорию или елемент меню из прочитанного файла
     * @param \SplFileInfo $splFileInfo
     * @return IMenuItem|MenuDirectory|null
     */
    private static function getMenuObject(\SplFileInfo $splFileInfo): IMenuItem|MenuDirectory|null
    {
        $title = $splFileInfo->getFilename();
        return $splFileInfo->isDir()
            ? new MenuDirectory($title, $title)
            : self::getMenuItem($splFileInfo);
    }

    /**
     * @param \SplFileInfo $splFileInfo
     * @return IMenuItem|null
     */
    private static function getMenuItem(\SplFileInfo $splFileInfo): ?IMenuItem
    {
        $page = new (get_class_from_file($splFileInfo));
        $page = PageService::repository()->getPage($page->getKey()) ?? $page;
        if (!$page->canAddToMenu()) return null;
        return $page->forMenu();
    }

    /**
     * Просмотреть директории от текущей к родительской для формирования
     * вложенности меню сохраняя иерархию папок
     *
     * @param IMenuDirectory|IMenuItem $item
     * @param \RecursiveIteratorIterator $iterator
     * @param IMenu $menu
     * @return IMenuDirectory|IMenuItem
     */
    private static function reduceInDepth(
        IMenuDirectory|IMenuItem $item,
        \RecursiveIteratorIterator $iterator,
        IMenu $menu
    ): IMenuDirectory|IMenuItem
    {
        for ($depth = $iterator->getDepth() - 1; $depth >= 0; $depth--) {
            $titleDir = $iterator->getSubIterator($depth)->current()->getFilename();
            $dir = $menu->find($titleDir); // найти в текущей версии меню, директорию родителя
            if ($dir) {
                if ($menu->find($item->key)) continue; // добавлен ли уже элемент в меню
                $dir->addItem($item);
            }
            else $item = new MenuDirectory($titleDir, $titleDir, [$item]);
        }

        return $item;
    }
}
