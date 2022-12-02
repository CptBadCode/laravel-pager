<?php

namespace Cptbadcode\LaravelPager\Contracts\Menu;

interface IMenuRepository
{
    public function getAll(): array;
    public function find(string $menuName): ?IMenu;
    public function getMenu(): ?array;
    public function addOrUpdate(string $menuName, IMenu $menu): IMenu;
    public function sort(string $menuName);
}
