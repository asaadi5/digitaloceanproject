<aside class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="assets/images/logo-icon-2.png" class="logo-icon" alt="logo icon">
        </div>
        <div>
            <h4 class="logo-text">لوحة الإدارة</h4>
        </div>
        <div class="toggle-icon ms-auto">
            <ion-icon name="menu-sharp"></ion-icon>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">

        {{-- لوحة التحكم --}}
        <a href="{{ route('admin_dashboard') }}" class="{{ Request::is('admin/dashboard') ? 'active' : '' }}">
            <div class="parent-icon">
                <ion-icon name="home-sharp"></ion-icon>
            </div>
            <div class="menu-title">لوحة التحكم</div>
        </a>

        {{-- اعدادات الموقع --}}
        <li>
            <a href="javascript:;" class="has-arrow {{ Request::is('admin/setting/*') ? 'active' : '' }}">
                <div class="parent-icon">
                    <ion-icon name="settings-outline"></ion-icon>
                </div>
                <div class="menu-title">اعدادات الموقع</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('admin_setting_logo_index') }}" class="{{ Request::is('admin/setting/logo/*') ? 'active' : '' }}">
                        <ion-icon name="arrow-forward-outline"></ion-icon>الشعار
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_setting_favicon_index') }}" class="{{ Request::is('admin/setting/favicon/*') ? 'active' : '' }}">
                        <ion-icon name="arrow-forward-outline"></ion-icon>رمز الصفحة
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_setting_banner_index') }}" class="{{ Request::is('admin/setting/banner/*') ? 'active' : '' }}">
                        <ion-icon name="arrow-forward-outline"></ion-icon>اللافتة
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_setting_footer_index') }}" class="{{ Request::is('admin/setting/footer/*') ? 'active' : '' }}">
                        <ion-icon name="arrow-forward-outline"></ion-icon>تذييل الصفحة
                    </a>
                </li>
            </ul>
        </li>

        {{-- قسم العقارات --}}
        <li>
            <a href="javascript:;" class="has-arrow {{ Request::is('admin/location/*') || Request::is('admin/type/*') || Request::is('admin/amenity/*') || Request::is('admin/property/*') ? 'active' : '' }}">
                <div class="parent-icon">
                    <ion-icon name="home-outline"></ion-icon>
                </div>
                <div class="menu-title">قسم العقارات</div>
            </a>
            <ul>
                <li>
                    <a href="{{ route('admin_location_index') }}" class="{{ Request::is('admin/location/*') ? 'active' : '' }}">
                        <ion-icon name="arrow-forward-outline"></ion-icon>الموقع
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_type_index') }}" class="{{ Request::is('admin/type/*') ? 'active' : '' }}">
                        <ion-icon name="arrow-forward-outline"></ion-icon>النوع
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_amenity_index') }}" class="{{ Request::is('admin/amenity/*') ? 'active' : '' }}">
                        <ion-icon name="arrow-forward-outline"></ion-icon>وسائل الراحة
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin_property_index') }}" class="{{ Request::is('admin/property/*') ? 'active' : '' }}">
                        <ion-icon name="arrow-forward-outline"></ion-icon>العقارات
                    </a>
                </li>
            </ul>
        </li>

        {{-- الباقات --}}
        <li>
            <a class="" href="{{ route('admin_package_index') }}" class="{{ Request::is('admin/package/*') ? 'active' : '' }}">
                <div class="parent-icon">
                    <ion-icon name="cube-outline"></ion-icon>
                </div>
                <div class="menu-title">الباقات</div>
            </a>
        </li>

        {{-- الطلبات --}}
        <li>
            <a class="" href="{{ route('admin_order_index') }}" class="{{ Request::is('admin/order/*') ? 'active' : '' }}">
                <div class="parent-icon">
                    <ion-icon name="cart-outline"></ion-icon>
                </div>
                <div class="menu-title">الطلبات</div>
            </a>
        </li>

        {{-- الزبائن --}}
        <li>
            <a class="" href="{{ route('admin_customer_index') }}" class="{{ Request::is('admin/customer/*') ? 'active' : '' }}">
                <div class="parent-icon">
                    <ion-icon name="people-outline"></ion-icon>
                </div>
                <div class="menu-title">الزبائن</div>
            </a>
        </li>

        {{-- الوكلاء --}}
        <li>
            <a class="" href="{{ route('admin_agent_index') }}" class="{{ Request::is('admin/agent/*') ? 'active' : '' }}">
                <div class="parent-icon">
                    <ion-icon name="person-outline"></ion-icon>
                </div>
                <div class="menu-title">الوكلاء</div>
            </a>
        </li>

        {{-- شهادات العملاء --}}
        <li>
            <a class="" href="{{ route('admin_testimonial_index') }}" class="{{ Request::is('admin/testimonial/*') ? 'active' : '' }}">
                <div class="parent-icon">
                    <ion-icon name="chatbubble-ellipses-outline"></ion-icon>
                </div>
                <div class="menu-title">شهادات العملاء</div>
            </a>
        </li>

        {{-- المدونة --}}
        <li>
            <a class="" href="{{ route('admin_post_index') }}" class="{{ Request::is('admin/post/*') ? 'active' : '' }}">
                <div class="parent-icon">
                    <ion-icon name="newspaper-outline"></ion-icon>
                </div>
                <div class="menu-title">المدونة</div>
            </a>
        </li>

        {{-- الاسئلة المتكررة --}}
        <li>
            <a class="" href="{{ route('admin_faq_index') }}" class="{{ Request::is('admin/faq/*') ? 'active' : '' }}">
                <div class="parent-icon">
                    <ion-icon name="help-circle-outline"></ion-icon>
                </div>
                <div class="menu-title">الاسئلة المتكررة</div>
            </a>
        </li>

        {{-- قسم الصفحات --}}
        <li>
            <a class="" href="{{ route('admin_page_index') }}" class="{{ Request::is('admin/page/*') ? 'active' : '' }}">
                <div class="parent-icon">
                    <ion-icon name="document-text-outline"></ion-icon>
                </div>
                <div class="menu-title">قسم الصفحات</div>
            </a>
        </li>

        {{-- المشتركين --}}
        <li>
            <a class="" href="{{ route('admin_subscriber_index') }}" class="{{ Request::is('admin/subscriber/*') ? 'active' : '' }}">
                <div class="parent-icon">
                    <ion-icon name="mail-outline"></ion-icon>
                </div>
                <div class="menu-title">المشتركين</div>
            </a>
        </li>

        {{-- تعديل الملف الشخصي --}}
        <li>
            <a class="" href="{{ route('admin_profile') }}" class="{{ Request::is('admin/profile') ? 'active' : '' }}">
                <div class="parent-icon">
                    <ion-icon name="create-outline"></ion-icon>
                </div>
                <div class="menu-title">تعديل الملف الشخصي</div>
            </a>
        </li>

        {{-- تسجيل الخروج --}}
        <li>
            <a class="" href="{{ route('admin_logout') }}">
                <div class="parent-icon">
                    <ion-icon name="log-out-outline"></ion-icon>
                </div>
                <div class="menu-title">تسجيل الخروج</div>
            </a>
        </li>

    </ul>
    <!--end navigation-->
</aside>
