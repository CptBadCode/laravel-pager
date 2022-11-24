<?php

namespace Cptbadcode\LaravelPager\Contracts;

interface IMenuRemover
{
    public function remove(array $menu, IPage ...$pages): array;
}
