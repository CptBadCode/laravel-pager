<?php

namespace Cptbadcode\LaravelPager\Helpers;

class MenuGenerator
{
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
                if (!$page->canAddToMenu()) continue;
                $path = [$page->getKey() => $page->toArray()];
            }

            for ($depth = $iterator->getDepth() - 1; $depth >= 0; $depth--) {
                $path = array($iterator->getSubIterator($depth)->current()->getFilename() => $path);
            }
            $menu = array_merge_recursive($menu, $path);
        }

        return $menu;
    }
}
