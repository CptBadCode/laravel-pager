<?php

namespace Cptbadcode\LaravelPager\Contracts;

interface IPageLoader
{
    public static function load(): IPageRepository;
}
