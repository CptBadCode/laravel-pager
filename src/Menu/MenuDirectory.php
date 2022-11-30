<?php

namespace Cptbadcode\LaravelPager\Menu;

use Cptbadcode\LaravelPager\Contracts\ResponsableArr;
use Cptbadcode\LaravelPager\Services\MenuService;
use Cptbadcode\LaravelPager\Contracts\Menu\{IMenuItem, IMenuDirectory};

class MenuDirectory implements IMenuDirectory, ResponsableArr
{
    public string $title;

    public string $key;

    protected array $items = [];

    public function __construct(string $title, string $key, array $items = [])
    {
        $this->title = $title;
        $this->key = $key;
        $this->items = $items;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * Поиск элемента меню по ключу
     * @param string $key
     * @return IMenuItem|IMenuDirectory|null
     */
    public function find(string $key): IMenuDirectory|IMenuItem|null
    {
        if ($this->key === $key) return $this;
        return MenuService::menuItemsHasKey($key, ...$this->getItems());
    }

    /**
     * @param IMenuItem|IMenuDirectory ...$items
     * @return void
     */
    public function addItems(IMenuItem|IMenuDirectory ...$items)
    {
        $this->items = array_merge($this->items, $items);
    }

    /**
     * @param IMenuItem|IMenuDirectory $item
     * @return void
     */
    public function addItem(IMenuItem|IMenuDirectory $item)
    {
        $this->items[] = $item;
    }

    /**
     * @param int $index
     * @param IMenuDirectory|IMenuItem $menuItem
     * @return void
     */
    public function update(int $index, IMenuDirectory|IMenuItem $menuItem)
    {
        $this->items[$index] = $menuItem;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return count($this->items) === 0;
    }

    /**
     * @param IMenuDirectory|IMenuItem $item
     * @return void
     */
    public function remove(IMenuDirectory|IMenuItem $item)
    {
        $this->items = array_values(array_filter($this->items, fn($current) => $current->key !== $item->key));
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_reduce($this->items, function($res, $item) {
            $key = $item->key ?? $item->title;
            $res[$key] = $item->toArray();
            return $res;
        }, []);
    }
}
