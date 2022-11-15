<?php

namespace Cptbadcode\LaravelPager\Services;

use Cptbadcode\LaravelPager\Contracts\IPage;
use Cptbadcode\LaravelPager\Contracts\Services\IDisableService;
use Cptbadcode\LaravelPager\Facades\PageFacade;

class DisableService implements IDisableService
{
    public function enable(string|IPage $page): void
    {
        ($page instanceof IPage)
            ? $page->enable()
            : $this->findAndEnable($page);
    }

    public function disable(string|Ipage $page): void
    {
        ($page instanceof IPage)
            ? $page->disable()
            : $this->findAndDisable($page);
    }

    public function findAndDisable(string $key): void
    {
        $page = PageFacade::repository()->getPageOrFail($key);
        $page->disable();
    }

    public function findAndEnable(string $key): void
    {
        $page = PageFacade::repository()->getPageOrFail($key);
        $page->enable();
    }
}
