<?php

namespace Cptbadcode\LaravelPager\Contracts\Menu;

use Cptbadcode\LaravelPager\Menu\MenuItem;

interface IMenuItem
{
    public function find(string $key): ?MenuItem;
}
