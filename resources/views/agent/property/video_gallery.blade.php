@extends('front.layouts.master')

@section('main_content')
    <!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-bs-image-src="../assets/images/banners/banner2.jpg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="">لوحة التحكم الخاصة بي</h1>
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">كل العقارات</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">معرض الفيديو </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Breadcrumb-->

    <!--video_gallery-->
    <section class="sptb">
        <div class="container-fluid px-0">
            <div class="row  g-0">

                <!-- الشريط الجانبي  -->
                <div class="col-xl-2 col-lg-3 col-md-12">
                    @include('agent.sidebar.index')
                </div>
                <!-- /الشريط الجانبي  -->

                <!-- المحتوى  -->
                <div class="col-xl-10 col-lg-9 col-md-12">
                    <div class="card mb-0">
                        <div class="card-header">
                            <h3 class="card-title">معرض الفيديو</h3>
                        </div>
                        <div class="card-body">
                            <h4>إضافة فيديو</h4>
                            <form action="{{ route('agent_property_video_gallery_store',$property->id) }}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <input type="text" name="video" class="form-control" placeholder="YouTube Video Id أو رابط YouTube">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <input type="submit" class="btn btn-primary btn-sm" value="إضافة">
                                    </div>
                                </div>
                            </form>

                            <h4 class="mt-4">الفيديوهات الموجودة</h4>
                            <div class="photo-all">
                                <div class="row">
                                    @if($videos->isEmpty())
                                        <div class="col-md-12">
                                            <div class="alert alert-primary">
                                                لا يوجد فيديوهات
                                            </div>
                                        </div>
                                    @else
                                        @foreach($videos as $item)
                                            <div class="col-md-6 col-lg-3">
                                                <div class="item item-delete">
                                                    <a class="video-button" href="http://www.youtube.com/watch?v={{ $item->video }}" target="_blank" rel="noopener">
                                                        <img src="http://img.youtube.com/vi/{{ $item->video }}/0.jpg" alt="">
                                                        <div class="icon">
                                                            <i class="far fa-play-circle"></i>
                                                        </div>
                                                        <div class="bg"></div>
                                                    </a>
                                                </div>
                                                <a href="{{ route('agent_property_video_gallery_delete',$item->id) }}" class="btn btn-danger delete-btn btn-sm text-white" title="حذف"><i class="fa fa-trash-o"></i></a>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /المحتوى -->

            </div>
        </div>
    </section>
    <!--/video_gallery-->
@endsection
