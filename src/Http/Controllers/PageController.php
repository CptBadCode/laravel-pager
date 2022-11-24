<?php

namespace Cptbadcode\LaravelPager\Http\Controllers;

use App\Http\Controllers\Controller;
use Cptbadcode\LaravelPager\PageService;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __invoke(Request $request)
    {
        $page = PageService::repository()->getPageOrFail($request->route()->getName());

        if ($page->isDisabled()) return abort(404);

        if ($page->hasActionToCall()) {
            $page->callAction(app(), $request->route());
        }

        return $page;
    }
}
