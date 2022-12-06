<?php

namespace Cptbadcode\LaravelPager\Traits;

use Cptbadcode\LaravelPager\PageService;

trait TemplatesPage
{
    protected string
        $template = PageService::DEFAULT_TEMPLATE;

    public function useTemplate(string $path)
    {
        $this->template = $path;
    }

    public function getHeaderLayout(): string
    {
        return PageService::headerForPage($this->getKey()) ?? $this->template.'.'.PageService::DEFAULT_HEADER;
    }

    public function getFooterLayout(): string
    {
        return PageService::footerForPage($this->getKey()) ?? $this->template.'.'.PageService::DEFAULT_FOOTER;
    }

    public function getBodyLayout(): string
    {
        return $this->template.'.'.PageService::DEFAULT_BODY;
    }
}
