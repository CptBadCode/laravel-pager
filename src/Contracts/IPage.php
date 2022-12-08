<?php

namespace Cptbadcode\LaravelPager\Contracts;

use Cptbadcode\LaravelPager\Menu\MenuItem;
use Illuminate\Container\Container;

interface IPage
{
    public function get(string $key): mixed;
    public function getKey(): string;
    public function setKey(string $value);
    public function getTitle(): string;
    public function getMiddleware(): array;
    public function setMiddleware(array $middleware = []);
    public function addMiddleware(array $middleware = []);
    public function forMenu(): MenuItem;
    public function renderData($request): array;
    public function callAction(Container $container, $route): mixed;
    public function hasActionToCall(): bool;
    public function canAddToMenu(): bool;
    public function getFooterLayout(): string;
    public function getHeaderLayout(): string;
    public function getBodyLayout(): string;
}
