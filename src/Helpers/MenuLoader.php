<?php

namespace Cptbadcode\LaravelPager\Helpers;

use Illuminate\Support\Facades\Cache;
use Cptbadcode\LaravelPager\PageService;
use Cptbadcode\LaravelPager\Contracts\{IMenuLoader, IMenuRepository};

class MenuLoader implements IMenuLoader
{
    public static function load(): IMenuRepository
    {
        $menuRepository = app(IMenuRepository::class);
        if (!Cache::has(PageService::CACHE_MENU_KEY)) {
            $menuRepository->setMenu(
                MenuGenerator::generateMenu(PageService::getRootPath())
            );
        }
        return $menuRepository;
    }
}
