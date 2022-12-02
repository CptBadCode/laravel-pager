<?php

namespace Cptbadcode\LaravelPager\Contracts\Menu;

use Cptbadcode\LaravelPager\Contracts\IPage;

interface IMenuRemover
{
    public function remove(array $menu, IPage ...$pages): array;
}
