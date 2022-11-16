<?php

use Cptbadcode\LaravelPager\PageService;
use Cptbadcode\LaravelPager\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

$pages = PageService::repository()->getPages();

foreach ($pages as $page) {
    Route::get($page->uri, PageController::class)
        ->middleware($page->getMiddleware())
        ->name($page->getKey());
}
