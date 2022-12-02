<?php

namespace Cptbadcode\LaravelPager\Contracts\Menu;

use Cptbadcode\LaravelPager\Contracts\IPage;

interface IMenu
{
    public function getMenu(): array;
    public function find(string $key): IMenuDirectory|IMenuItem|null;
    public function setMenu(array $menu): void;
    public function updateMenuByPages(IPage ...$pages);
    public function updateMenu();
    public function removeFromMenu(IPage ...$pages);
    public function sort();
}
