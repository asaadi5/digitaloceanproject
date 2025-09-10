@props(['propertyId', 'wished' => false])

<a href="javascript:void(0);" class="item-card2-icons-r bg-secondary wishlist-toggle" data-id="{{ $propertyId }}">
    <i class="fa {{ $wished ? 'fa-heart' : 'fa-heart-o' }}"></i>
</a>
