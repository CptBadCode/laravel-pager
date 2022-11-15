<?php

namespace Cptbadcode\LaravelPager\Repositories;

use Cptbadcode\LaravelPager\BasePage;
use Cptbadcode\LaravelPager\Contracts\IPage;
use Cptbadcode\LaravelPager\Contracts\IPageRepository;

class PageRepository implements IPageRepository
{
    protected array $pages = [];

    public function getPages(): array
    {
        return $this->pages;
    }

    public function getPage(string $key): IPage|null
    {
        return $this->pages[$key] ?? null;
    }

    public function getPageOrFail(string $key): ?IPage
    {
        $page = $this->getPage($key);
        throw_if(!$page, 'RuntimeException', "Page {$key} not found", 404);
        return $page;
    }

    public function addPage(string $className): bool
    {
        if ($this->isPage($className)) {
            $page = new $className;
            $this->pages[$page->getKey()] = $page;
            return true;
        }

        return false;
    }

    protected function isPage(string $className): bool
    {
        return class_exists($className) && is_subclass_of($className, BasePage::class);
    }
}
