<?php

namespace Cptbadcode\LaravelPager\Contracts;

interface IPageLoader
{
    public function loadPages(): void;
    public function loadMenu(): void;
    public function getPages(): array;
    public function getMenu(): array;
}
