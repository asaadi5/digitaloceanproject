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
                        <li class="breadcrumb-item"><a href="javascript:;"><ion-icon name="home-outline"></ion-icon></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"> منشورات المدونة </li>
                        <li class="breadcrumb-item active" aria-current="page">تعديل منشور</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end breadcrumb-->

        <div class="main-content">
            <section class="section">
                <div class="section-header d-flex justify-content-between">
                    <h1>تعديل منشور</h1>
                    <div class="ml-auto">
                        <a href="{{ route('admin_post_index') }}" class="btn btn-primary">عرض الكل<i class="fas fa-eye"></i></a>
                    </div>
                </div>
                <div class="section-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form action="{{ route('admin_post_update',$post->id) }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label> الصورة الحالية </label>
                                            <div>
                                                <img src="{{ asset('uploads/'.$post->photo) }}" alt="لا يوجد" class="table-img">
                                            </div>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label class="form-label">تغيير الصورة</label>
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
                                        <div class="form-group mb-3">
                                            <label>العنوان *</label>
                                            <input type="text" class="form-control" name="title" value="{{ $post->title }}">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>المعرّف النصّي *</label>
                                            <input type="text" class="form-control" name="slug" value="{{ $post->slug }}">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>وصف قصير *</label>
                                            <textarea name="short_description" class="form-control h_100" cols="30" rows="10">{{ $post->short_description }}</textarea>
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>الوصف *</label>
                                            <textarea name="description" class="form-control editor" cols="30" rows="10">{{ $post->description }}</textarea>
                                        </div>
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">تحديث</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
    <!-- end page content-->
</div>
<!--end page content wrapper-->
@endsection
