<?php

namespace Cptbadcode\LaravelPager\Contracts;

interface IPageRepository extends IRepository
{
    public function addPage(string $className): bool;
    public function addPages(array $pages): void;
    public function getPages(): array;
    public function getPagesByKeys(string ...$keys): array;
    public function getPage(string $key): IPage|null;
    public function getPageOrFail(string $key): ?IPage;
}
