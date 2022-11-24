<?php

namespace Cptbadcode\LaravelPager\Contracts\Services;

use Cptbadcode\LaravelPager\Contracts\IPage;

interface IDisableService
{
    public static function disable(string|Ipage $page): IPage;
    public static function enable(string|Ipage $page): IPage;
    public static function findAndDisable(string $key): IPage;
    public static function findAndEnable(string $key): IPage;
}
