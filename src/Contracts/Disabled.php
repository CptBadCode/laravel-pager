<?php

namespace Cptbadcode\LaravelPager\Contracts;

interface Disabled
{
    public function isDisabled(): bool;
    public function disable(): self;
    public function enable(): self;
}
