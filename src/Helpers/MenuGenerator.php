<?php

namespace Cptbadcode\LaravelPager\Helpers;

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

            if ($splFileInfo->isDir()) {
                $title = $splFileInfo->getFilename();
                $path = new MenuDirectory($title, $title);
            }
            else {
                $page = new (get_class_from_file($splFileInfo));
                $page = PageService::repository()->getPage($page->getKey()) ?? $page;
                if (!$page->canAddToMenu()) continue;
                $path = $page->forMenu();
            }

            for ($depth = $iterator->getDepth() - 1; $depth >= 0; $depth--) {
                $titleDir = $iterator->getSubIterator($depth)->current()->getFilename();
                $dir = $menu->find($titleDir);
                if ($dir) {
                    if ($menu->find($path->key)) continue;
                    $dir->addItem($path);

                }
                else {
                    $path = new MenuDirectory($titleDir, $titleDir, [$path]);
                }
            }
            if(!$menu->find($path->key)) $menu->add($path);
        }
        return $menu;
    }
}
