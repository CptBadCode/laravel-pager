<?php

namespace Cptbadcode\LaravelPager\Contracts\Services;

use Cptbadcode\LaravelPager\Contracts\IPage;

interface IDisableService
{
    public function disable(string|Ipage $page): void;
    public function enable(string|Ipage $page): void;
}
