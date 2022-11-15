<?php

namespace Cptbadcode\LaravelPager\Helpers;

use Cptbadcode\LaravelPager\{PageService};
use Cptbadcode\LaravelPager\Contracts\{IPageLoader};
use Illuminate\Support\Facades\File;

class PageLoader implements IPageLoader
{
    protected array $pages = [];

    protected array $menu = [];

    public function loadPages(): void
    {
        $files = $this->load();
        foreach ($files as $file) {
            $this->pages[] = get_class_from_file($file);
        }
    }

    public function getPages(): array
    {
        return $this->pages;
    }

    public function loadMenu(): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator(self::getRootPath()),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
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
            $this->menu = array_merge_recursive($this->menu, $path);
        }
    }

    public function getMenu(): array
    {
        return $this->menu;
    }

    private function load(): array
    {
        return File::allFiles(self::getRootPath());
    }

    private static function getRootPath(): string
    {
        return base_path(PageService::PAGE_NAMESPACE);
    }
}
