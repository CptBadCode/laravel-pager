<?php

namespace Cptbadcode\LaravelPager\Contracts;

interface IMenuLoader
{
    public static function load(string $nameMenu, string $filepath, array $attributes = []);
    public static function loadDefault(array $attributes = []);
}
