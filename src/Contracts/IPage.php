<?php

namespace Cptbadcode\LaravelPager\Contracts;

interface IPage
{
    public function getKey(): string;
    public function setKey(string $value);
    public function canAddToMenu(): bool;
    public function getTitle(): string;
    public function getMiddleware(): array;
    public function setMiddleware(array $middleware = []);
    public function toArray(): array;
    public function renderData($request): array;
}
