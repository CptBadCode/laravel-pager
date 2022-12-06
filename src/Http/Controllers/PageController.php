<?php

namespace Cptbadcode\LaravelPager\Http\Controllers;

use App\Http\Controllers\Controller;
use Cptbadcode\LaravelPager\Contracts\IPage;
use Cptbadcode\LaravelPager\PageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class PageController extends Controller
{
    protected IPage $page;

    public function __construct()
    {
        $name = Route::currentRouteName();

        $this->page = PageService::repository()->getPageOrFail($name);

        $this->middleware($this->page->getMiddleware());
    }

    public function __invoke(Request $request)
    {
        if ($this->page->hasActionToCall()) {
            $this->page->callAction(app(), $request->route());
        }

        return $this->page;
    }
}
