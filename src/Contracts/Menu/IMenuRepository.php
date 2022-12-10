<?php

namespace Cptbadcode\LaravelPager\Contracts\Menu;

use Cptbadcode\LaravelPager\Contracts\IRepository;

interface IMenuRepository extends IRepository
{
    public function getAll(): array;
    public function find(string $menuName): ?IMenu;
    public function getMenu(): ?IMenu;
    public function addOrUpdate(string $menuName, IMenu $menu): IMenu;
    public function sort(string $menuName);
}
