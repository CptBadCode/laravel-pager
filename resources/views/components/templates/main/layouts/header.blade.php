<nav>
    <div class="logo"></div>

    <x-laravel-pager::menu :list="$page['menu']"></x-laravel-pager::menu>
</nav>
<header>
    <div class="headline">
        <div class="inner">
            <h1>{{ $page['title'] }}</h1>
            <p>Выбранный язык: {{ $page['lang'] }}</p>
        </div>
    </div>
</header>
