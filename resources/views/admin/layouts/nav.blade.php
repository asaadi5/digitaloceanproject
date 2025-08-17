<header class="top-header">
    <nav class="navbar navbar-expand gap-3">
        <div class="mobile-menu-button">
            <ion-icon name="menu-sharp"></ion-icon>
        </div>

        <div class="top-navbar-right ms-auto">

            <ul class="navbar-nav align-items-center">
                <li class="nav-item">
                    <a href="{{ route('home') }}" target="_blank"
                       class="btn btn-primary btn-sm rounded-pill px-3 py-1 fs-6 shadow-sm bg-primary">
                        واجهة المستخدم
                    </a>
                </li>
                <!-- Full Screen -->
                <li class="nav-item">
                    <a class="nav-link " id="fullscreenLink" href="javascript:;">
                        <div class="full-screen">
                            <ion-icon id="full-screen-icon" name="scan-sharp"></ion-icon>
                        </div>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link dark-mode-icon" href="javascript:;">
                        <div class="mode-icon">
                            <ion-icon name="moon-sharp"></ion-icon>
                        </div>
                    </a>
                </li>

                <li class="nav-item dropdown dropdown-user-setting">
                    <a class="nav-link dropdown-toggle dropdown-toggle-nocaret" href="javascript:;" data-bs-toggle="dropdown">
                        <div class="user-setting">
                            <img src="{{ asset('uploads/'.Auth::guard('admin')->user()->photo) }}" class="user-img" alt="">
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('admin_profile') }}">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <ion-icon name="person-outline"></ion-icon>
                                    </div>
                                    <div class="ms-3"><span>تعديل الملف الشخصي</span></div>
                                </div>
                            </a>
                        </li>

                        <li>
                            <a class="dropdown-item" href="{{ route('admin_logout') }}">
                                <div class="d-flex align-items-center">
                                    <div>
                                        <ion-icon name="log-out-outline"></ion-icon>
                                    </div>
                                    <div class="ms-3"><span>تسجيل الخروج</span></div>
                                </div>
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>

        </div>
    </nav>
</header>
<!--end top header-->

