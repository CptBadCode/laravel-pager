<?php

namespace Cptbadcode\LaravelPager\Contracts;

interface IMenuLoader
{
    public static function load(string $nameMenu, string $filepath);
    public static function loadDefault();
}
