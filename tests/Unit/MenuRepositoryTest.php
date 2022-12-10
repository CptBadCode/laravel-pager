<?php

namespace Cptbadcode\LaravelPager\Tests;

use Cptbadcode\LaravelPager\Contracts\Menu\IMenu;
use Cptbadcode\LaravelPager\Menu\Menu;
use Cptbadcode\LaravelPager\Menu\MenuDirectory;
use Cptbadcode\LaravelPager\Menu\MenuItem;
use Cptbadcode\LaravelPager\Services\MenuService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class MenuRepositoryTest extends PageTestCase
{
    public function test_find_menu()
    {
        Artisan::call('make:page', ['name' => 'About']);
        Artisan::call('make:page', ['name' => 'Client\Home']);
        Artisan::call('make:page', ['name' => 'Client\Dashboard']);

        MenuService::loadMenu();

        $menu = MenuService::repository()->find(MenuService::BASE_MENU_KEY);
        $this->assertInstanceOf(IMenu::class, $menu);
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
        ], $menu->toArray());
    }

    public function test_add_or_update()
    {
        $menu = new Menu([
            new MenuItem(
                title: 'test_title',
                uri: '/',
                key: 'home_page',
                isDisabled: false,
                sortKey: 0
            ),
            new MenuItem(
                title: 'test_title_2',
                uri: '/',
                key: 'about_page',
                isDisabled: false,
                sortKey: 0
            ),
            new MenuDirectory(
                title: 'test_dir',
                key: 'test_dir',
                items: [
                    new MenuItem(
                        title: 'test_title_4',
                        uri: '/',
                        key: 'profile_page',
                        isDisabled: false,
                        sortKey: 0
                    )
                ]
            )
        ]);

        MenuService::repository()->addOrUpdate('test', $menu);

        $this->assertEquals($menu, MenuService::repository()->find('test'));
        $this->assertArrayHasKey('test', MenuService::repository()->getAll());
    }

    public function test_cache_and_clear()
    {
        $menu = new Menu([
            new MenuItem(
                title: 'test_title',
                uri: '/',
                key: 'home_page',
                isDisabled: false,
                sortKey: 0
            ),
        ]);

        MenuService::repository()->addOrUpdate('test', $menu);
        MenuService::repository()->cache();

        $this->assertTrue(Cache::has(MenuService::CACHE_KEY));

        MenuService::repository()->clear();

        $this->assertFalse(Cache::has(MenuService::CACHE_KEY));
        $this->assertEquals([], MenuService::repository()->getAll());
    }
}
