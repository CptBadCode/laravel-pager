<?php

namespace Cptbadcode\LaravelPager\Views\Components;

use Illuminate\View\Component;

class Footer extends Component
{
    protected array $page = [];

    public function __construct(array $page)
    {
        $this->page = $page;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('laravel-pager::components.'.$this->page['footer_layout']);
    }
}
