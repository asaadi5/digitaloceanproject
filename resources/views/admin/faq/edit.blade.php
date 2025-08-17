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
                        <li class="breadcrumb-item active" aria-current="page"> الأسئلة المتكررة </li>
                        <li class="breadcrumb-item active" aria-current="page">تعديل سؤال</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end breadcrumb-->

        <div class="main-content">
            <section class="section">
                <div class="section-header d-flex justify-content-between">
                    <h1>تعديل سؤال</h1>
                    <div class="ml-auto">
                        <a href="{{ route('admin_faq_index') }}" class="btn btn-primary">عرض الكل<i class="fas fa-eye"></i></a>
                    </div>
                </div>
                <div class="section-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <form action="{{ route('admin_faq_update',$faq->id) }}" method="post">
                                        @csrf
                                        <div class="form-group mb-3">
                                            <label>السؤال *</label>
                                            <input type="text" class="form-control" name="question" value="{{ $faq->question }}">
                                        </div>
                                        <div class="form-group mb-3">
                                            <label>الإجابة *</label>
                                            <textarea name="answer" class="form-control h_200" cols="30" rows="10">{{ $faq->answer }}</textarea>
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
