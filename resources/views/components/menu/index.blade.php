@props([
    'list'
])

<ul>
    @foreach($list as $item)
        <x-laravel-pager::menu.item :item="$item"></x-laravel-pager::menu.item>
    @endforeach
</ul>
