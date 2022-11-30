<?php

namespace Cptbadcode\LaravelPager\Helpers;

use Illuminate\Support\Facades\Cache;
use Cptbadcode\LaravelPager\PageService;
use Cptbadcode\LaravelPager\Contracts\{IPageLoader, IPageRepository};
use Illuminate\Support\Facades\File;

class PageLoader implements IPageLoader
{
    public static function load(): IPageRepository
    {
        $pageRepository = app(IPageRepository::class);

        if (!Cache::has(PageService::CACHE_PAGE_KEY)) {
            $files = File::allFiles(PageService::getRootPath());
            foreach ($files as $file) {
                $className = get_class_from_file($file);
                $pageRepository->addPage($className);
            }
        }
        return $pageRepository;
    }
}
