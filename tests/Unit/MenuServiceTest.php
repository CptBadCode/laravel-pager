<?php

namespace Cptbadcode\LaravelPager\Tests;


use Cptbadcode\LaravelPager\Helpers\PageLoader;
use Cptbadcode\LaravelPager\PageService;
use Cptbadcode\LaravelPager\Services\MenuService;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class MenuServiceTest extends PageTestCase
{
    public function test_load_menu()
    {
        Artisan::call('make:page', ['name' => 'About']);
        Artisan::call('make:page', ['name' => 'Client\Home']);
        Artisan::call('make:page', ['name' => 'Client\Dashboard']);

        MenuService::loadMenu([
            'client' => [
                'title' => 'Профиль',
                'sortKey' => 1
            ]
        ]);

        $this->assertEquals([
            "Client" => [
                "home_page" => [
                    "title" => "HomePage",
                    "uri" => "home_page",
                    "is_disabled" => false,
                    "key" => "home_page",
                    "sort" => 0
                ],
                "dashboard_page" => [
                    "title" => "DashboardPage",
                    "uri" => "dashboard_page",
                    "is_disabled" => false,
                    "key" => "dashboard_page",
                    "sort" => 0
                ]
            ],
            "about_page" => [
                "title" => "AboutPage",
                "uri" => "about_page",
                "is_disabled" => false,
                "key" => "about_page",
                "sort" => 0
            ]
        ], MenuService::repository()->getMenu()->toArray());
    }

    public function test_generate_menu()
    {
        Artisan::call('make:page', [
            'name' => 'About',
            '--base_dir' => 'App/TestPages'
        ]);

        Artisan::call('make:page', [
            'name' => 'Home',
            '--base_dir' => 'App/TestPages'
        ]);

        MenuService::generateMenu('test', base_path('App\\TestPages'));

        $this->assertEquals([
            "about_page" => [
                "title" => "AboutPage",
                "uri" => "about_page",
                "is_disabled" => false,
                "key" => "about_page",
                "sort" => 0
            ],
            "home_page" => [
                "title" => "HomePage",
                "uri" => "home_page",
                "is_disabled" => false,
                "key" => "home_page",
                "sort" => 0
            ],
        ], MenuService::repository()->find('test')->toArray());

        app(Filesystem::class)->deleteDirectory(base_path('App\\TestPages'));
    }

    public function test_generate_menu_from_pages()
    {
        Artisan::call('make:page', ['name' => 'About']);
        Artisan::call('make:page', ['name' => 'Client\Home']);
        Artisan::call('make:page', ['name' => 'Client\Dashboard']);

        PageService::loadPages();
        MenuService::loadMenu();
        MenuService::generateMenuFromPages('custom', ['home_page','dashboard_page']);

        $this->assertEquals([
            "dashboard_page" => [
                "title" => "DashboardPage",
                "uri" => "dashboard_page",
                "is_disabled" => false,
                "key" => "dashboard_page",
                "sort" => 0
            ],
            "home_page" => [
                "title" => "HomePage",
                "uri" => "home_page",
                "is_disabled" => false,
                "key" => "home_page",
                "sort" => 0
            ],
        ], MenuService::repository()->find('custom')->toArray());

        $this->assertEquals([
            "Client" => [
                "dashboard_page" => [
                    "title" => "DashboardPage",
                    "uri" => "dashboard_page",
                    "is_disabled" => false,
                    "key" => "dashboard_page",
                    "sort" => 0
                ],
                "home_page" => [
                    "title" => "HomePage",
                    "uri" => "home_page",
                    "is_disabled" => false,
                    "key" => "home_page",
                    "sort" => 0
                ],
            ],
            "about_page" => [
                "title" => "AboutPage",
                "uri" => "about_page",
                "is_disabled" => false,
                "key" => "about_page",
                "sort" => 0
            ],
        ], MenuService::repository()->find(MenuService::BASE_MENU_KEY)->toArray());
    }

    public function test_menu_items_has_key()
    {
        Artisan::call('make:page', ['name' => 'About']);
        Artisan::call('make:page', ['name' => 'Client\News']);
        Artisan::call('make:page', ['name' => 'Home']);
        Artisan::call('make:page', ['name' => 'Client\Profile']);
        PageLoader::load();

        MenuService::loadMenu();

        $menu = MenuService::repository()->getMenu()->getItems();
        $this->assertEquals($menu[0], MenuService::menuItemsHasKey('about_page', ...$menu));
        $this->assertEquals($menu[2], MenuService::menuItemsHasKey('home_page', ...$menu));
        $this->assertEquals($menu[1]->getItems()[1], MenuService::menuItemsHasKey('profile_page', ...$menu));
        $this->assertEquals($menu[1]->getItems()[0], MenuService::menuItemsHasKey('news_page', ...$menu));
        $this->assertNull(MenuService::menuItemsHasKey('wrong_page', ...$menu));
    }

    public function test_update_menu_where_has()
    {
        Artisan::call('make:page', ['name' => 'About']);
        Artisan::call('make:page', ['name' => 'Client\News']);
        Artisan::call('make:page', ['name' => 'Home']);
        Artisan::call('make:page', ['name' => 'Client\Profile']);

        PageService::loadPages();
        MenuService::loadMenu();
        MenuService::generateMenuFromPages('custom', ['home_page','news_page']);
        MenuService::generateMenuFromPages('test', ['profile_page','about_page']);

        $page = PageService::repository()->getPage('news_page');
        $profilePage = PageService::repository()->getPage('profile_page');
        PageService::disablePage($page, $profilePage);

        MenuService::updateMenuWhereHas($page, $profilePage);

        $this->assertEquals([
            "news_page" => [
                "title" => "NewsPage",
                "uri" => "news_page",
                "is_disabled" => true,
                "key" => "news_page",
                "sort" => 0
            ],
            "home_page" => [
                "title" => "HomePage",
                "uri" => "home_page",
                "is_disabled" => false,
                "key" => "home_page",
                "sort" => 0
            ],
        ], MenuService::repository()->find('custom')->toArray());

        $this->assertEquals([
            "about_page" => [
                "title" => "AboutPage",
                "uri" => "about_page",
                "is_disabled" => false,
                "key" => "about_page",
                "sort" => 0
            ],
            "profile_page" => [
                "title" => "ProfilePage",
                "uri" => "profile_page",
                "is_disabled" => true,
                "key" => "profile_page",
                "sort" => 0
            ],
        ], MenuService::repository()->find('test')->toArray());

        $this->assertEquals([
            "about_page" => [
                "title" => "AboutPage",
                "uri" => "about_page",
                "is_disabled" => false,
                "key" => "about_page",
                "sort" => 0
            ],
            "Client" => [
                "news_page" => [
                    "title" => "NewsPage",
                    "uri" => "news_page",
                    "is_disabled" => true,
                    "key" => "news_page",
                    "sort" => 0
                ],
                "profile_page" => [
                    "title" => "ProfilePage",
                    "uri" => "profile_page",
                    "is_disabled" => true,
                    "key" => "profile_page",
                    "sort" => 0
                ],
            ],
            "home_page" => [
                "title" => "HomePage",
                "uri" => "home_page",
                "is_disabled" => false,
                "key" => "home_page",
                "sort" => 0
            ],

        ], MenuService::repository()->find(MenuService::BASE_MENU_KEY)->toArray());
    }

    public function test_remove_from_menu_where_has()
    {
        Artisan::call('make:page', ['name' => 'About']);
        Artisan::call('make:page', ['name' => 'Client\News']);
        Artisan::call('make:page', ['name' => 'Client\Profile']);
        Artisan::call('make:page', ['name' => 'Home']);

        PageService::loadPages();
        MenuService::loadMenu();
        MenuService::generateMenuFromPages('custom', ['home_page','news_page']);
        MenuService::generateMenuFromPages('test', ['profile_page','about_page', 'home_page']);

        $page = PageService::repository()->getPage('home_page');
        $profilePage = PageService::repository()->getPage('profile_page');
        MenuService::removeFromMenuWhereHas($page, $profilePage);

        $this->assertEquals([
            "about_page" => [
                "title" => "AboutPage",
                "uri" => "about_page",
                "is_disabled" => false,
                "key" => "about_page",
                "sort" => 0
            ],
        ], MenuService::repository()->find('test')->toArray());

        $this->assertEquals([
            "news_page" => [
                "title" => "NewsPage",
                "uri" => "news_page",
                "is_disabled" => false,
                "key" => "news_page",
                "sort" => 0
            ],
        ], MenuService::repository()->find('custom')->toArray());
    }
}
