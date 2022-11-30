@props([
    'item'
])
@menuDir($item)
    @foreach($item->getItems() as $current)
        <x-laravel-pager::menu.item :item="$current"></x-laravel-pager::menu.item>
    @endforeach
@else
    <li>
        <a @class(['disabled' => $item->isDisabled]) href="{{ $item->uri }}">
            {{ $item->title }}
        </a>
    </li>
@endmenuDir
