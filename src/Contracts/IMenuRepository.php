<?php

namespace Cptbadcode\LaravelPager\Contracts;

interface IMenuRepository
{
    public function getMenu(): array;
    public function setMenu(array $menu): void;
    public function updateMenuByPage(IPage $page);
    public function updateMenu();
    public function removeFromMenu(IPage ...$pages);
}
