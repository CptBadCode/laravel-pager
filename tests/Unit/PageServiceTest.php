<?php

namespace Cptbadcode\LaravelPager\Tests\Unit;

use App\Models\User;
use Cptbadcode\LaravelPager\PageService;
use Cptbadcode\LaravelPager\Services\MenuService;
use Cptbadcode\LaravelPager\Tests\PageTestCase;
use Cptbadcode\LaravelPager\Tests\RefreshPages;
use Illuminate\Support\Facades\Artisan;

class PageServiceTest extends PageTestCase
{
    use RefreshPages;

    public function test_header_for_page()
    {
        $this->assertNull(PageService::headerForPage('home_page'));
        $this->assertNull(PageService::headerForPage('about_page'));

        PageService::headerComponentUsing('header.component.path', 'home_page', 'about_page');
        PageService::headerComponentUsing('header2.component.path', 'company_page', 'about_page');

        $this->assertEquals('header.component.path', PageService::headerForPage('home_page'));
        $this->assertNull(PageService::headerForPage('wrong_page'));
        $this->assertEquals('header2.component.path', PageService::headerForPage('about_page'));
        $this->assertEquals('header2.component.path', PageService::headerForPage('company_page'));
    }

    public function test_footer_for_page()
    {
        $this->assertNull(PageService::footerForPage('home_page'));
        $this->assertNull(PageService::footerForPage('about_page'));

        PageService::footerComponentUsing('footer.component.path', 'home_page', 'about_page');
        PageService::footerComponentUsing('footer2.component.path', 'company_page', 'about_page');

        $this->assertEquals('footer.component.path', PageService::footerForPage('home_page'));
        $this->assertNull(PageService::footerForPage('wrong_page'));
        $this->assertEquals('footer2.component.path', PageService::footerForPage('about_page'));
        $this->assertEquals('footer2.component.path', PageService::footerForPage('company_page'));
    }

    public function test_get_instance_pages()
    {
        Artisan::call('make:page', ['name' => 'About']);
        Artisan::call('make:page', ['name' => 'Catalog']);
        Artisan::call('make:page', ['name' => 'Company']);
        Artisan::call('make:page', ['name' => 'Home']);
        PageService::loadPages();

        $this->assertEquals([
            new (PageService::PAGE_NAMESPACE.'\\AboutPage'),
            new (PageService::PAGE_NAMESPACE.'\\CatalogPage'),
        ], PageService::getInstancePages([
            'about_page',
            'catalog_page'
        ]));

        $this->assertEquals([
            new (PageService::PAGE_NAMESPACE.'\\AboutPage'),
            'client' => [
                new (PageService::PAGE_NAMESPACE.'\\AboutPage'),
                new (PageService::PAGE_NAMESPACE.'\\HomePage')
            ],
            new (PageService::PAGE_NAMESPACE.'\\CompanyPage'),
        ], PageService::getInstancePages([
            'about_page',
            'client' => [
                'about_page',
                'home_page'
            ],
            'company_page'
        ]));

        $this->assertThrows(fn() => PageService::getInstancePages([
            'about_page',
            'client' => [
                'about_page',
                'wrong_page',
                'home_page'
            ],
            'company_page'
        ]), \RuntimeException::class, 'Page wrong_page not found');
    }

    public function test_remove_from_menu()
    {
        Artisan::call('make:page', ['name' => 'Client\About']);
        Artisan::call('make:page', ['name' => 'Client\Catalog']);
        Artisan::call('make:page', ['name' => 'Company']);
        Artisan::call('make:page', ['name' => 'Home']);
        PageService::loadPages();
        MenuService::loadMenu();

        PageService::removeFromMenu(MenuService::BASE_MENU_KEY, 'catalog_page');
        $this->assertEquals([
            "Client" => [
                "about_page" => [
                    "title" => "AboutPage",
                    "uri" => "about_page",
                    "is_disabled" => false,
                    "key" => "about_page",
                    "sort" => 0
                ]
            ],
            "company_page" => [
                "title" => "CompanyPage",
                "uri" => "company_page",
                "is_disabled" => false,
                "key" => "company_page",
                "sort" => 0
            ],
            "home_page" => [
                "title" => "HomePage",
                "uri" => "home_page",
                "is_disabled" => false,
                "key" => "home_page",
                "sort" => 0
            ]
        ], MenuService::repository()->getMenu()->toArray());

        PageService::removeFromMenu(MenuService::BASE_MENU_KEY, 'about_page');

        $this->assertEquals([
            "company_page" => [
                "title" => "CompanyPage",
                "uri" => "company_page",
                "is_disabled" => false,
                "key" => "company_page",
                "sort" => 0
            ],
            "home_page" => [
                "title" => "HomePage",
                "uri" => "home_page",
                "is_disabled" => false,
                "key" => "home_page",
                "sort" => 0
            ]
        ], MenuService::repository()->getMenu()->toArray());

        MenuService::loadMenu();

        $page = $this->pageRepository->getPage('company_page');

        PageService::removeFromMenu(MenuService::BASE_MENU_KEY, 'home_page', $page);

        $this->assertEquals([
            "Client" => [
                "about_page" => [
                    "title" => "AboutPage",
                    "uri" => "about_page",
                    "is_disabled" => false,
                    "key" => "about_page",
                    "sort" => 0
                ],
                "catalog_page" => [
                    "title" => "CatalogPage",
                    "uri" => "catalog_page",
                    "is_disabled" => false,
                    "key" => "catalog_page",
                    "sort" => 0
                ],
            ],
        ], MenuService::repository()->getMenu()->toArray());

    }

    public function test_enable_page()
    {
        Artisan::call('make:page', ['name' => 'Company']);
        Artisan::call('make:page', ['name' => 'Home']);
        PageService::loadPages();
        MenuService::loadMenu();

        PageService::disablePage('home_page');
        PageService::enablePage('home_page');

        $page = $this->pageRepository->getPage('home_page');
        $this->assertNotTrue($page->isDisabled());

        $this->assertEquals([
            "company_page" => [
                "title" => "CompanyPage",
                "uri" => "company_page",
                "is_disabled" => false,
                "key" => "company_page",
                "sort" => 0
            ],
            "home_page" => [
                "title" => "HomePage",
                "uri" => "home_page",
                "is_disabled" => false,
                "key" => "home_page",
                "sort" => 0
            ]
        ], MenuService::repository()->getMenu()->toArray());
    }

    public function test_apply_middleware()
    {
        Artisan::call('make:page', ['name' => 'Company']);
        Artisan::call('make:page', ['name' => 'Home']);
        Artisan::call('make:page', ['name' => 'About']);
        PageService::loadPages();

        $page = $this->pageRepository->getPage('home_page');
        $page->addMiddleware(['web']);
        PageService::applyMiddleware(['auth'], $page, 'company_page');

        $this->assertCount(2, $page->getMiddleware());
        $this->assertEquals(['web', 'auth'], $page->getMiddleware());

        $page = $this->pageRepository->getPage('company_page');
        $this->assertCount(1, $page->getMiddleware());
        $this->assertEquals(['auth'], $page->getMiddleware());
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function test_use_template_for_page()
    {
        Artisan::call('make:page', ['name' => 'Home']);
        Artisan::call('make:page', ['name' => 'Company']);
        PageService::loadPages();

        PageService::useTemplateForPage('default', 'home_page');
        $page = $this->pageRepository->getPage('home_page');

        $this->assertEquals('default.'.PageService::DEFAULT_HEADER, $page->getHeaderLayout());
        $this->assertEquals('default.'.PageService::DEFAULT_FOOTER, $page->getFooterLayout());
        $this->assertEquals('default.'.PageService::DEFAULT_BODY, $page->getBodyLayout());

        PageService::headerComponentUsing('header.path', 'home_page');
        $this->assertEquals('header.path', $page->getHeaderLayout());

        $page = $this->pageRepository->getPage('company_page');

        $this->assertEquals(PageService::DEFAULT_TEMPLATE.'.'.PageService::DEFAULT_HEADER, $page->getHeaderLayout());
        $this->assertEquals(PageService::DEFAULT_TEMPLATE.'.'.PageService::DEFAULT_FOOTER, $page->getFooterLayout());
        $this->assertEquals(PageService::DEFAULT_TEMPLATE.'.'.PageService::DEFAULT_BODY, $page->getBodyLayout());
    }

    public function test_disable_page()
    {
        Artisan::call('make:page', ['name' => 'Company']);
        Artisan::call('make:page', ['name' => 'Home']);
        PageService::loadPages();
        MenuService::loadMenu();

        PageService::disablePage('company_page');

        $page = $this->pageRepository->getPage('company_page');
        $this->assertTrue($page->isDisabled());

        $this->assertEquals([
            "company_page" => [
                "title" => "CompanyPage",
                "uri" => "company_page",
                "is_disabled" => true,
                "key" => "company_page",
                "sort" => 0
            ],
            "home_page" => [
                "title" => "HomePage",
                "uri" => "home_page",
                "is_disabled" => false,
                "key" => "home_page",
                "sort" => 0
            ]
        ], MenuService::repository()->getMenu()->toArray());
    }

    public function test_apply_middleware_all()
    {
        Artisan::call('make:page', ['name' => 'Company']);
        Artisan::call('make:page', ['name' => 'Home']);
        PageService::loadPages();

        PageService::applyMiddlewareAll(['auth']);

        $page = $this->pageRepository->getPage('home_page');
        $page->addMiddleware(['my_middleware']);
        $this->assertCount(2, $page->getMiddleware());
        $this->assertEquals(['auth', 'my_middleware'], $page->getMiddleware());

        $page = $this->pageRepository->getPage('company_page');
        $this->assertCount(1, $page->getMiddleware());
        $this->assertEquals(['auth'], $page->getMiddleware());
    }

    public function test_is_page()
    {
        Artisan::call('make:page', ['name' => 'Company']);
        PageService::loadPages();

        $page = PageService::repository()->getPage('company_page');

        $this->assertFalse(PageService::isPage('home_page'));
        $this->assertTrue(PageService::isPage($page));
        $this->assertFalse(PageService::isPage(new User()));
        $this->assertThrows(fn() => PageService::isPage(new class{}));
    }
}
