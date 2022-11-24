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

    public function disable(): self
    {
        $this->setDisable(true);
        return $this;
    }

    public function enable(): self
    {
        $this->setDisable(false);
        return $this;
    }
}
