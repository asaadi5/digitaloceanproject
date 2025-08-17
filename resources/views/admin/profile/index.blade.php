@extends('admin.layouts.master')

@section('main_content')
    @include('admin.layouts.nav')
    @include('admin.layouts.sidebar')
    <!-- start page content wrapper-->
    <div class="page-content-wrapper">
        <!-- start page content-->
        <div class="page-content">
            <!--start breadcrumb-->
            <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
                <div class="breadcrumb-title pe-3">لوحة التحكم</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0 align-items-center">
                            <li class="breadcrumb-item"><a href="javascript:;">
                                    <ion-icon name="home-outline"></ion-icon>
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"> تعديل الملف الشخصي </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="mb-4">
                                        <label class="form-label"> الاسم *</label>
                                        <input type="text" class="form-control" name="name" value="">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label"> البريد الإلكتروني *</label>
                                        <input type="text" class="form-control" name="email" value="">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label"> كلمة المرور </label>
                                        <input type="password" class="form-control" name="password">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label"> أعد كتابة كلمة المرور </label>
                                        <input type="password" class="form-control" name="confirm_password">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label"></label>
                                        <button type="submit" class="btn btn-primary"> حفظ التغييرات </button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <img src="assets/images/avatars/02.png"
                                         class="figure-img img-fluid img-thumbnail">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <input type="file" id="dash_photo" name="photo" class="d-none" accept="image/*">
                                        <label for="dash_photo"
                                               class="btn btn-outline-primary btn-sm rounded-pill px-4 d-inline-flex align-items-center gap-2">
                                            <ion-icon name="image-sharp"></ion-icon>
                                            تحميل صورة
                                        </label>
                                        <span id="dash_photoName"
                                              class="text-muted small text-truncate"
                                              style="max-width: 260px;"
                                              aria-live="polite">لم يتم اختيار ملف</span>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page content-->
    </div>
    <!-- end page content wrapper-->
@endsection
