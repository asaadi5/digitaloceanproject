<div class="card overflow-hidden">
    <div class="card-header">
        <h3 class="card-title">لوحة التحكم الخاصة بي</h3>
    </div>
    <div class="card-body text-center item-user">
        <div class="profile-pic">
            <div class="profile-pic-img">
                <span class="bg-success dots" data-bs-toggle="tooltip" data-bs-placement="top" title="online"></span>
                @php
                    $agent = Auth::guard('agent')->user();
                    $photo = $agent->photo ?? null; // الصورة محفوظة بـ uploads/
                @endphp
                <img
                    src="{{ $photo ? asset('uploads/'.$photo) : asset('assets/images/faces/male/25.jpg') }}"
                    class="brround" alt="user">
            </div>
            <a href="{{ route('agent_profile') }}" class="text-dark">
                <h4 class="mt-3 mb-0 font-weight-semibold">{{ $agent->name }}</h4>
            </a>
        </div>
    </div>

    <aside class="doc-sidebar my-dash">
        <div class="app-sidebar__user clearfix">
            <ul class="side-menu">
                <li>
                    <a class="side-menu__item {{ request()->routeIs('agent_dashboard') ? 'active' : '' }}"
                       href="{{ route('agent_dashboard') }}">
                        <i class="icon icon-user"></i>
                        <span class="side-menu__label ms-2">لوحة التحكم</span>
                    </a>
                </li>
                <li>
                    <a class="side-menu__item {{ request()->routeIs('agent_payment') ? 'active' : '' }}"
                       href="{{ route('agent_payment') }}">
                        <i class="icon icon-credit-card"></i>
                        <span class="side-menu__label ms-2">الدفع</span>
                    </a>
                </li>
                <li>
                    <a class="side-menu__item {{ request()->routeIs('agent_order') ? 'active' : '' }}"
                       href="{{ route('agent_order') }}">
                        <i class="icon icon-basket"></i>
                        <span class="side-menu__label ms-2">الطلبات</span>
                    </a>
                </li>
                <li>
                    <a class="side-menu__item {{ request()->routeIs('agent_property_create') ? 'active' : '' }}"
                       href="{{ route('agent_property_create') }}">
                        <i class="icon icon-plus"></i>
                        <span class="side-menu__label ms-2">إضافة عقار</span>
                    </a>
                </li>
                <li>
                    <a class="side-menu__item {{ request()->routeIs('agent_property_index') ? 'active' : '' }}"
                       href="{{ route('agent_property_index') }}">
                        <i class="icon icon-home"></i>
                        <span class="side-menu__label ms-2">كل العقارات </span>
                    </a>
                </li>
                <li>
                    <a class="side-menu__item {{ request()->routeIs('agent_message*') ? 'active' : '' }}"
                       href="{{ route('agent_message') }}">
                        <i class="icon icon-envelope"></i>
                        <span class="side-menu__label ms-2">الرسائل </span>
                    </a>
                </li>
                <li>
                    <a class="side-menu__item {{ request()->routeIs('agent_profile') ? 'active' : '' }}"
                       href="{{ route('agent_profile') }}">
                        <i class="icon icon-user"></i>
                        <span class="side-menu__label ms-2">تعديل الملف الشخصي</span>
                    </a>
                </li>
                <!--
                <li>
                  <a class="side-menu__item" href="settings.html"><i class="icon icon-settings"></i><span class="side-menu__label ms-2">الإعدادات </span></a>
                </li>
                -->
                <li>
                    <a class="side-menu__item" href="{{ route('agent_logout') }}">
                        <i class="icon icon-power"></i>
                        <span class="side-menu__label ms-2">تسجيل الخروج</span>
                    </a>
                </li>
            </ul>
        </div>
    </aside>
</div>
