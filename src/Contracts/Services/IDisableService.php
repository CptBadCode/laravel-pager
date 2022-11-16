<?php

namespace Cptbadcode\LaravelPager\Contracts\Services;

use Cptbadcode\LaravelPager\Contracts\IPage;

interface IDisableService
{
    public static function disable(string|Ipage $page): void;
    public static function enable(string|Ipage $page): void;
    public static function findAndDisable(string $key): void;
    public static function findAndEnable(string $key): void;
}
