<?php

namespace Cptbadcode\LaravelPager\Contracts;

use Illuminate\Container\Container;

interface IPage
{
    public function get(string $key): mixed;
    public function getKey(): string;
    public function setKey(string $value);
    public function getTitle(): string;
    public function getMiddleware(): array;
    public function setMiddleware(array $middleware = []);
    public function toArray(): array;
    public function renderData($request): array;
    public function callAction(Container $container, $route): mixed;
    public function hasActionToCall(): bool;
    public function canAddToMenu(): bool;
    public function removeFromMenu();
    public function getFooterLayout(): string;
    public function getHeaderLayout(): string;
}
