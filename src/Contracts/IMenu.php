<?php

namespace Cptbadcode\LaravelPager\Contracts;

use Cptbadcode\LaravelPager\Contracts\Menu\{IMenuItem, IMenuDirectory};

interface IMenu
{
    public function getMenu(): array;
    public function find(string $key): IMenuDirectory|IMenuItem|null;
    public function setMenu(array $menu): void;
    public function updateMenuByPages(IPage ...$pages);
    public function updateMenu();
    public function removeFromMenu(IPage ...$pages);
}
