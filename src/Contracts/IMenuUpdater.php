<?php

namespace Cptbadcode\LaravelPager\Contracts;

interface IMenuUpdater
{
    public function updateAll(array $menu): array;
    public function updateByPage(array $menu, IPage $page): array;
    public function updateByPages(array $menu, IPage ...$pages): array;
}
