<?php

namespace Cptbadcode\LaravelPager\Contracts;

interface IMenuLoader
{
    public static function load(): IMenuRepository;
}
