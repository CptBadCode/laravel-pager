<?php

namespace Cptbadcode\LaravelPager\Contracts;

interface IPageRepository
{
    public function addPage(string $className): bool;
    public function getPages(): array;
    public function getPage(string $key): IPage|null;
    public function getPageOrFail(string $key): ?IPage;
}
