<?php

namespace Cptbadcode\LaravelPager\Menu;

use Cptbadcode\LaravelPager\Contracts\Menu\IMenuItem;
use Cptbadcode\LaravelPager\Contracts\ResponsableArr;

class MenuItem implements ResponsableArr, IMenuItem
{
    public static string
        $menuTitleKey = 'title',
        $menuUriKey = 'uri',
        $menuDisableKey = 'is_disabled',
        $menuKeyKey = 'key',
        $menuSortKey = 'sort';

    public function __construct(
        public string $title,
        public string $uri,
        public string $key,
        public bool $isDisabled = false,
        public int $sortKey = 0
    ){}

    public function find(string $key): ?MenuItem
    {
        return $this->key === $key ? $this : null;
    }

    public function toArray(): array
    {
        return [
            self::$menuTitleKey => $this->title,
            self::$menuUriKey => $this->uri,
            self::$menuDisableKey => $this->isDisabled,
            self::$menuKeyKey => $this->key,
            self::$menuSortKey => $this->sortKey,
        ];
    }
}
