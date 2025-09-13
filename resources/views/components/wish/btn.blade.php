@props(['propertyId', 'wished' => false])

@php
    $isWebUser   = Auth::guard('web')->check();     // مستخدم عادي (Customer)
    $isAgentUser = Auth::guard('agent')->check();   // وكيل (Agent)
@endphp

@if($isAgentUser)
    {{-- وكيل: ممنوع — نعرض الزر بشكل معطّل مع Tooltip --}}
    <a href="javascript:void(0);" class="item-card2-icons-r bg-secondary disabled"
       title="الوكلاء لا يمكنهم استخدام المفضلة"
       style="opacity:.5; cursor:not-allowed; pointer-events:none;">
        <i class="fa fa-heart-o"></i>
    </a>
@else
    {{-- زائر أو مستخدم web --}}
    <a href="{{ $isWebUser ? 'javascript:void(0);' : route('login') }}"
       class="item-card2-icons-r bg-secondary {{ $isWebUser ? 'wishlist-toggle' : '' }}"
       data-id="{{ $propertyId }}">
        <i class="fa {{ $wished ? 'fa-heart' : 'fa-heart-o' }}"></i>
    </a>
@endif
