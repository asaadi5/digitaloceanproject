<div class="header-main">
    <div class="top-bar">
        <div class="container">
            <div class="row">

                <div class="col-xl-8 col-lg-8 col-sm-4 col-7">
                    <div class="top-bar-left d-flex">
                        <div class="clearfix">
                            <ul class="socials">
                                <li>
                                    <a class="social-icon text-dark" href="{{ $global_setting->facebook }}"><i class="fa fa-facebook"></i></a>
                                </li>
                                <li>
                                    <a class="social-icon text-dark" href="{{ $global_setting->whatsapp }}"><i class="fa fa-whatsapp"></i></a>
                                </li>
                                <li>
                                    <a class="social-icon text-dark" href="{{ $global_setting->telegram }}"><i class="fa fa-telegram"></i></a>
                                </li>
                                <li>
                                    <a class="social-icon text-dark" href="{{ $global_setting->youtube }}"><i class="fa fa-youtube"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xl-4 col-lg-4 col-sm-8 col-5">
                    @php
                        $isUser  = Auth::guard()->check();        // web user
                        $isAdmin = Auth::guard('admin')->check(); // admin
                    @endphp

                    <div class="top-bar-right">
                        <ul class="custom">

                            {{-- ضيف (غير مسجل دخول) --}}
                            @if(!$isUser && !$isAdmin)
                                <li>
                                    <a href="{{ route('registration') }}" class="text-dark">
                                        <i class="fa fa-user me-1"></i> <span>التسجيل</span>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('login') }}" class="text-dark">
                                        <i class="fa fa-sign-in me-1"></i> <span>تسجيل الدخول</span>
                                    </a>
                                </li>
                            @endif

                            {{-- مستخدم أو أدمن --}}
                            @if($isUser || $isAdmin)
                                <li class="dropdown">
                                    <a href="javascript:void(0);" class="text-dark" data-bs-toggle="dropdown">
                                        <i class="fa fa-home me-1"></i><span> لوحة التحكم</span>
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">

                                        {{-- ملفي الشخصي (للاثنين) --}}
                                        @if($isUser)
                                            <a href="{{ route('profile') }}" class="dropdown-item">
                                                <i class="dropdown-icon icon icon-user"></i> ملفي الشخصي
                                            </a>
                                        @elseif($isAdmin)
                                            <a href="{{ route('admin_profile') }}" class="dropdown-item">
                                                <i class="dropdown-icon icon icon-user"></i> ملفي الشخصي
                                            </a>
                                        @endif

                                        {{-- المفضلة (فقط للمستخدم) --}}
                                        @if($isUser)
                                            <a href="{{ route('wishlist') }}" class="dropdown-item">
                                                <i class="dropdown-icon icon icon-heart"></i> المفضلة
                                            </a>
                                        @endif

                                        {{-- تسجيل الخروج --}}
                                        @if($isUser)
                                            <a href="{{ route('logout') }}" class="dropdown-item">
                                                <i class="dropdown-icon icon icon-power"></i> تسجيل الخروج
                                            </a>
                                        @elseif($isAdmin)
                                            <a href="{{ route('admin_logout') }}" class="dropdown-item">
                                                <i class="dropdown-icon icon icon-power"></i> تسجيل الخروج
                                            </a>
                                        @endif

                                    </div>
                                </li>
                            @endif

                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Duplex Houses Header -->
    <div class="sticky">
        <div class="horizontal-header clearfix ">
            <div class="container">
                <a id="horizontal-navtoggle" class="animated-arrow"><span></span></a>
                <span class="smllogo">
						<a href="{{route('home')}}">
							<img src="{{ asset('uploads/'.$global_setting->logo) }}" class="mobile-light-logo" width="120" alt="pic"/>
							<img src="{{ asset('uploads/'.$global_setting->logo) }}" class="mobile-dark-logo" width="120" alt="pic"/>
						</a>
					</span>
                <a href="tel:245-6325-3256" class="callusbtn"><i class="fa fa-phone" aria-hidden="true"></i></a>
            </div>
        </div>
    </div>
    <!-- /Duplex Houses Header -->

    <div class="horizontal-main bg-dark-transparent clearfix">
        <div class="horizontal-mainwrapper container clearfix">
            <div class="desktoplogo">
                <a href="index.html"><img src="../assets/images/brand/logo1.png" alt=""></a>
            </div>
            <div class="desktoplogo-1">
                <a href="index.html"><img src="../assets/images/brand/logo1.png" alt=""></a>
            </div>
            <!--Nav-->
            <nav class="horizontalMenu clearfix d-md-flex">
                <ul class="horizontalMenu-list">
                    <li aria-haspopup="true">
                        <a href="{{ route('home') }}" class="active">الرئيسية <span class="fa m-0"></span></a>
                    </li>

                    <li aria-haspopup="true">
                        <a href="javascript:void(0);">عقارات <span class="fa fa-caret-down m-0"></span></a>
                        <ul class="sub-menu">
                            <li aria-haspopup="true"><a href="{{ route('properties.purpose','sale') }}">للبيع</a></li>
                            <li aria-haspopup="true"><a href="{{ route('properties.purpose','rent') }}">للإيجار</a></li>
                        </ul>
                    </li>

                    <li aria-haspopup="true">
                        <a href="javascript:void(0);">الفئات <span class="fa fa-caret-down m-0"></span></a>
                        <ul class="sub-menu">

                            {{-- سكني --}}
                            <li aria-haspopup="true">
                                <a href="{{ route('properties.category','residential') }}">
                                    سكني<i class="fa fa-angle-right float-end mt-1 d-none d-lg-block"></i>
                                </a>
                                <ul class="sub-menu">
                                    <li aria-haspopup="true"><a href="{{ route('properties.category',['category'=>'residential','type'=>'apartment']) }}">شقة</a></li>
                                    <li aria-haspopup="true"><a href="{{ route('properties.category',['category'=>'residential','type'=>'traditional-house']) }}">بيت عربي</a></li>
                                    <li aria-haspopup="true"><a href="{{ route('properties.category',['category'=>'residential','type'=>'villa']) }}">فيلا</a></li>
                                    <li aria-haspopup="true"><a href="{{ route('properties.category',['category'=>'residential','type'=>'roof-annex']) }}">ملحق</a></li>
                                </ul>
                            </li>

                            {{-- أراضي --}}
                            <li aria-haspopup="true">
                                <a href="{{ route('properties.category','lands') }}">
                                    أراضي<i class="fa fa-angle-right float-end mt-1 d-none d-lg-block"></i>
                                </a>
                                <ul class="sub-menu">
                                    <li aria-haspopup="true"><a href="{{ route('properties.category',['category'=>'lands','type'=>'agricultural-land']) }}">زراعية</a></li>
                                    <li aria-haspopup="true"><a href="{{ route('properties.category',['category'=>'lands','type'=>'construction-land']) }}">بناء</a></li>
                                </ul>
                            </li>

                            {{-- تجاري وخدمي --}}
                            <li aria-haspopup="true">
                                <a href="{{ route('properties.category','commercial') }}">
                                    تجاري وخدمي<i class="fa fa-angle-right float-end mt-1 d-none d-lg-block"></i>
                                </a>
                                <ul class="sub-menu">
                                    <li aria-haspopup="true"><a href="{{ route('properties.category',['category'=>'commercial','type'=>'office']) }}">مكتب</a></li>
                                    <li aria-haspopup="true"><a href="{{ route('properties.category',['category'=>'commercial','type'=>'shop']) }}">محل تجاري</a></li>
                                    <li aria-haspopup="true"><a href="{{ route('properties.category',['category'=>'commercial','type'=>'warehouse']) }}">مستودع</a></li>
                                </ul>
                            </li>

                            {{-- ترفيهي --}}
                            <li aria-haspopup="true">
                                <a href="{{ route('properties.category','recreational') }}">
                                    ترفيهي<i class="fa fa-angle-right float-end mt-1 d-none d-lg-block"></i>
                                </a>
                                <ul class="sub-menu">
                                    <li aria-haspopup="true"><a href="{{ route('properties.category',['category'=>'recreational','type'=>'chalet']) }}">شاليه</a></li>
                                    <li aria-haspopup="true"><a href="{{ route('properties.category',['category'=>'recreational','type'=>'pool']) }}">مزرعة</a></li>
                                </ul>
                            </li>

                        </ul>
                    </li>

                    <li aria-haspopup="true"> <a href="{{ route('agents') }}">الوكلاء</a></li>
                    <li aria-haspopup="true"> <a href="{{ route('pricing') }}">أسعار الإشتراكات</a></li>
                    <li aria-haspopup="true"> <a href="{{ route('blog') }}">المدونة</a></li>
                    <li aria-haspopup="true"> <a href="{{ route('about_us') }}">من نحن</a></li>
                    <li aria-haspopup="true"> <a href="{{ route('contact') }}"><span class="hmarrow"></span></a></li>
                </ul>


                <ul class="mb-0">
                    @if(Auth::guard('web')->check())
                        <li aria-haspopup="true" class="mt-3 d-none d-lg-block top-postbtn">
                            <span><a class="btn btn-secondary" href="{{ route('dashboard') }}">لوحة التحكم</a></span>
                        </li>
                    @elseif(Auth::guard('agent')->check())
                        <li aria-haspopup="true" class="mt-3 d-none d-lg-block top-postbtn">
                            <span><a class="btn btn-secondary" href="{{ route('agent.dashboard') }}">لوحة التحكم</a></span>
                        </li>
                    @else
                        <li aria-haspopup="true" class="mt-3 d-none d-lg-block top-postbtn">
                            <span><a class="btn btn-secondary" href="{{ route('login') }}">انشر إعلان عقاري </a></span>
                        </li>
                    @endif

                </ul>

            </nav>
            <!--Nav-->
        </div>
    </div>
</div>
