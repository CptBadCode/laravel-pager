<?php

namespace Cptbadcode\LaravelPager\Repositories;

use Cptbadcode\LaravelPager\Contracts\{IPage, IPageRepository};
use Cptbadcode\LaravelPager\PageService;
use Illuminate\Support\Facades\Cache;

class PageRepository implements IPageRepository
{
    protected array $pages = [];

    public function __construct()
    {
        $this->pages = Cache::get(PageService::CACHE_KEY) ?? [];
    }

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

    public function getPagesByKeys(string ...$keys): array
    {
        return array_reduce($keys, function($res, $key) {
            $page = $this->getPage($key);
            if ($page) $res[] = $page;
            return $res;
        }, []);
    }

    public function addPage(string $className): bool
    {
        if (PageService::isPage($className)) {
            $page = new $className;
            $this->pages[$page->getKey()] = $page;

            return true;
        }

        return false;
    }

    public function addPages(array $pages): void
    {
        foreach ($pages as $page) {
            $this->addPage($page);
        }
    }

    public function cache(): void
    {
        Cache::forever(PageService::CACHE_KEY, $this->pages);
    }

    public function clear()
    {
        $this->pages = [];
        Cache::forget(PageService::CACHE_KEY);
    }
}
