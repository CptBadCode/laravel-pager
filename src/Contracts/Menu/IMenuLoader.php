<?php

namespace Cptbadcode\LaravelPager\Contracts\Menu;

interface IMenuLoader
{
    public static function load(string $nameMenu, string $filepath, array $attributes = []);
    public static function loadDefault(array $attributes = []);
    public static function loadFromPages(string $nameMenu, array $pages): void;
}
