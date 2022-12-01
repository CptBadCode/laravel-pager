<?php

namespace Cptbadcode\LaravelPager\Contracts\Menu;

interface IMenuDirectory
{
    public function getItems(): array;
    public function isEmpty(): bool;
    public function find(string $key): IMenuDirectory|IMenuItem|null;
    public function remove(IMenuDirectory|IMenuItem $item);
    public function addItem(IMenuItem|IMenuDirectory $item);
    public function addItems(IMenuItem|IMenuDirectory ...$items);
    public function sortItems();
}
