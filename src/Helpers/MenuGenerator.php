<?php

namespace Cptbadcode\LaravelPager\Helpers;

use Cptbadcode\LaravelPager\PageService;

class MenuGenerator
{
    public static string $menuTitleKey = 'title',
                         $menuUriKey = 'uri',
                         $menuDisableKey = 'is_disabled';

    public static function getMenuTemplate(): array
    {
        return [
            self::$menuTitleKey => '',
            self::$menuUriKey => '/',
            self::$menuDisableKey => false
        ];
    }

    public static function generateMenu(string $path): array
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($path),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        $menu = [];
        foreach ($iterator as $splFileInfo) {
            if ($iterator->isDot()) continue;

            if ($splFileInfo->isDir()) {
                $path = [$splFileInfo->getFilename() => []];
            }
            else {
                $page = new (get_class_from_file($splFileInfo));
                $page = PageService::repository()->getPage($page->getKey()) ?? $page;
                if (!$page->canAddToMenu()) continue;
                $path = [$page->getKey() => $page->toArray()];
            }

            for ($depth = $iterator->getDepth() - 1; $depth >= 0; $depth--) {
                if (!self::checkPath($path)) continue;
                $path = array($iterator->getSubIterator($depth)->current()->getFilename() => $path);
            }
            if (!self::checkPath($path)) continue;
            $menu = array_merge_recursive($menu, $path);
        }

        return $menu;
    }

    /**
     * Если папка пустая, то удалить из результатов
     * @param array $path
     * @return bool
     */
    private static function checkPath(array &$path): bool
    {
        $k = key($path);
        if (!isset($path[$k])) return false;
        if (!count($path[$k])) {
            unset($path[$k]);
            return false;
        }
        return true;
    }
}
