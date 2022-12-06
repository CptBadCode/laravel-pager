<?php

namespace Cptbadcode\LaravelPager\Contracts;

interface IRepository
{
    public function cache(): void;
    public function clear();
}
