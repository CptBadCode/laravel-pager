<?php

namespace Cptbadcode\LaravelPager\Http\Controllers;

use App\Http\Controllers\Controller;
use Cptbadcode\LaravelPager\Facades\PageFacade;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function __invoke(Request $request)
    {
        $page = PageFacade::repository()->getPageOrFail($request->route()->getName());

        if ($page->isDisabled()) return abort(404);

        return $page;
    }
}
