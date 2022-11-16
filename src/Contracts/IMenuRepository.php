<?php

namespace Cptbadcode\LaravelPager\Contracts;

interface IMenuRepository
{
    public function getMenu(): array;
    public function updateMenu(array $menu): void;
}
