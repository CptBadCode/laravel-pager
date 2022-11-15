<?php

namespace Cptbadcode\LaravelPager\Contracts;

interface Disabled
{
    public function isDisabled(): bool;
    public function disable(): void;
    public function enable(): void;
}
