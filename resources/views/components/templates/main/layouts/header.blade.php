<nav>
    <div class="logo"></div>
    @if($page['menu'])
    <ul>
        @foreach($page['menu'] as $menu)
            <li @class([
                'disabled' => $menu['is_disabled']
            ])>
                <a href="{{ $menu['uri'] }}">{{ $menu['title'] }}</a>
            </li>
        @endforeach
    </ul>
    @endif
</nav>
<header>
    <div class="headline">
        <div class="inner">
            <h1>Hello 2</h1>
            <p>Scroll down the page</p>
        </div>
    </div>
</header>
