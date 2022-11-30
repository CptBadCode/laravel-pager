<?php

namespace Cptbadcode\LaravelPager\Contracts;

interface IMenuRepository
{
    public function getAll(): array;
    public function find(string $key): ?IMenu;
    public function getMenu(): ?array;
    public function addOrUpdate(string $key, IMenu $menu): IMenu;
}
