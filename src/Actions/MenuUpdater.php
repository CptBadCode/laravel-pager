<?php

namespace Cptbadcode\LaravelPager\Actions;

use Cptbadcode\LaravelPager\Contracts\IMenuUpdater;
use Cptbadcode\LaravelPager\Contracts\IPage;
use Cptbadcode\LaravelPager\PageService;

class MenuUpdater extends MenuAction implements IMenuUpdater
{
    public function updateAll(array $menu): array
    {
        $pages = PageService::repository()->getPages();

        $this->doRecursive($menu, ...$pages);
        return $menu;
    }

    public function updateByPage(array $menu, IPage $page): array
    {
        $this->doRecursive($menu, $page);

        return $menu;
    }

    protected function doRecursive(array &$menu, IPage ...$pages): void
    {
        array_walk($menu, function (&$item, $pageKey) use ($pages) {
            $filtered = array_filter($pages, fn($page) => $page->getKey() === $pageKey);
            $page = array_shift($filtered);
            if ($page) $item = $page->toArray();
            else if ($this->isDir($item)) $this->doRecursive($item, ...$pages);
        });
    }
}
