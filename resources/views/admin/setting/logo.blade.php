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
                <div class="breadcrumb-title pe-3">إعدادات الموقع</div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0 align-items-center">
                            <li class="breadcrumb-item"><a href="javascript:;"><ion-icon name="home-outline"></ion-icon></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">الشعار</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->

            <div class="card">
                <div class="card-header bg-primary text-light">
                    <h6 class="mb-0">تعديل شعار الموقع</h6>
                </div>
                <div class="card-body">
                    <form>
                        <div class="mb-4 d-flex flex-column gap-3 align-items-center justify-content-center">
                            <label class="badge rounded-pill bg-primary-subtle text-secondary px-4 py-2 fs-6">الشعار الحالي</label>
                            <figure class="figure m-0 text-end rounded-circle">
                                <img src="assets/images/logo-icon-3.png"
                                     class="figure-img img-fluid img-thumbnail"
                                     alt="الشعار الحالي">
                            </figure>
                        </div>
                        <div class="mb-4 d-flex flex-column gap-3 align-items-center justify-content-center">
                            <input type="file" id="photo" name="photo" class="d-none" accept="image/*">

                            <label for="photo" class="btn btn-outline-primary btn-sm rounded-pill px-4">
                                <ion-icon name="image-sharp"></ion-icon>
                                تغيير الصورة
                            </label>
                        </div>
                        <div class="text-start mt-3">
                            <button type="button" class="btn btn-primary px-4">حفظ التغييرات</button>
                        </div>
                    </form>
                </div>
            </div>
            <!--end row-->
        </div>
        <!-- end page content-->
    </div>
    <!--end page content wrapper-->
@endsection
