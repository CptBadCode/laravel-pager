<?php

namespace Cptbadcode\LaravelPager\Traits;

trait Disabled
{
    protected bool $disabled = false;

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    protected function setDisable(bool $value)
    {
        $this->disabled = $value;
    }

    public function disable(): void
    {
        $this->setDisable(true);
    }

    public function enable(): void
    {
        $this->setDisable(false);
    }
}
