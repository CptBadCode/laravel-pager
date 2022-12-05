<?php

namespace Cptbadcode\LaravelPager\Views\Components;

use Cptbadcode\LaravelPager\PageService;
use Illuminate\View\Component;

class Body extends Component
{
    public array $page = [];

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
        return view(($this->page['body_layout'] ?? PageService::DEFAULT_TEMPLATE.'.'.PageService::DEFAULT_BODY));
    }
}
