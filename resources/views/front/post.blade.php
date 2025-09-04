@extends('front.layouts.master')

@section('main_content')
    <!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-bs-image-src="../assets/images/banners/banner2.jpg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="">تفاصيل المقال</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

    @php
        // الصورة: كل صورك بصيغة "post1.jpg" داخل public/uploads
        $img = $post->photo ? asset('uploads/'.$post->photo) : asset('assets/images/photos/28.jpg');
        $comments = $post->comments ?? collect();
        $commentsCount = $post->comments_count ?? $comments->count();
    @endphp

        <!--Post Details-->
    <section class="sptb h-100">
        <div class="container">
            <div class="row">
                <!--Sidebar-->
                <div class="col-xl-4 col-lg-4 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('blog') }}" method="get">
                                <div class="input-group">
                                    <input type="text"
                                           name="q"
                                           class="form-control br-tl-3  br-bl-3"
                                           placeholder="بحث"
                                           value="{{ request('q') }}">
                                    <button type="submit" class="btn btn-primary br-tr-3  br-br-3">
                                        بحث
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>


                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Categories</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-catergory">
                                <div class="item-list">
                                    <ul class="list-group mb-0">
                                        @php
                                            // نستخدم نفس نمط الأيقونات/الألوان بالتناوب للحفاظ على الشكل
                                            $icons = [
                                                ['i'=>'fa fa-building','bg'=>'bg-primary text-primary'],
                                                ['i'=>'fa fa-bed','bg'=>'bg-success text-success'],
                                                ['i'=>'fa fa-building-o','bg'=>'bg-info text-info'],
                                                ['i'=>'fa fa-home','bg'=>'bg-warning text-warning'],
                                                ['i'=>'fa fa-building','bg'=>'bg-danger text-danger'],
                                                ['i'=>'fa fa-home','bg'=>'bg-blue text-blue'],
                                                ['i'=>'fa fa-building-o','bg'=>'bg-secondary text-pink'],
                                            ];
                                        @endphp

                                        @foreach(($types ?? collect()) as $k => $type)
                                            @php $ico = $icons[$k % count($icons)]; @endphp
                                            <li class="list-group-item">
                                                <a href="{{ route('blog', ['type' => $type->slug]) }}" class="text-dark">
                                                    <i class="{{ $ico['i'] }} {{ $ico['bg'] }}"></i>
                                                    <span>{{ $type->name }}</span>
                                                    <span class="badgetext badge rounded-pill badge-light mb-0 mt-1">{{ $type->posts_count }}</span>
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">وسوم شائعة</h3>
                        </div>
                        <div class="card-body">
                            <div class="product-tags clearfix">
                                <ul class="list-unstyled mb-0">
                                    <li><a href="javascript:void(0);">عقارات</a></li>
                                    <li><a href="javascript:void(0);">منازل</a></li>
                                    <li><a href="javascript:void(0);">شقق</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/Sidebar-->

                <!--Post Body-->
                <div class="col-xl-8 col-lg-8 col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="item7-card-img br-5">
                                <img src="{{ $img }}" alt="{{ $post->title }}" class="w-100">
                                <div class="item7-card-text">
                                    <span class="badge badge-info fs-20">{{ optional($post->type)->name ?? 'بدون تصنيف' }}</span>
                                </div>
                            </div>

                            <div class="item7-card-desc d-flex mb-2 mt-3">
                                <a href="javascript:void(0);" class="text-muted">
                                    <i class="fa fa-calendar-o text-muted me-2"></i>
                                    {{ optional($post->created_at)->format('Y-m-d') }}
                                </a>
                                <div class="ms-auto">
                                    <a href="javascript:void(0);" class="text-muted">
                                        <i class="fa fa-comment-o text-muted me-2"></i>{{ $commentsCount }} تعليقات
                                    </a>
                                </div>
                            </div>

                            <a href="javascript:void(0);" class="text-dark">
                                <h2 class="font-weight-semibold">{{ $post->title }}</h2>
                            </a>

                            {!! $post->description !!}
                        </div>
                    </div>

                    <!-- Comments -->
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">التعليقات</h3>
                        </div>
                        <div class="card-body p-0">
                            @forelse($comments as $comment)
                                <div class="media mt-0 p-5">
                                    <div class="d-flex me-3">
                                        <a href="javascript:void(0);">
                                            <img class="media-object brround" alt="avatar" src="../assets/images/faces/male/1.jpg">
                                        </a>
                                    </div>
                                    <div class="media-body">
                                        <h5 class="mt-0 mb-1 font-weight-semibold">
                                            {{ $comment->author_name }}
                                            <span class="fs-14 ms-2"> 4.5 <i class="fa fa-star text-yellow"></i></span>
                                        </h5>
                                        <small class="text-muted">
                                            <i class="fa fa-calendar"></i>
                                            {{ optional($comment->created_at)->format('Y-m-d') }}
                                            <i class=" ms-3 fa fa-clock-o"></i>
                                            {{ optional($comment->created_at)->format('H:i') }}
                                            <i class=" ms-3 fa fa-map-marker"></i>
                                            البرازيل
                                        </small>
                                        <p class="font-13 mb-2 mt-2">{{ $comment->body }}</p>
                                        <a href="javascript:void(0);" class="me-2 btn btn-default btn-sm" data-bs-toggle="modal" data-bs-target="#Comment"><span class="">تعليق</span></a>
                                    </div>
                                </div>
                            @empty
                                <div class="p-5 text-center text-muted">لا توجد تعليقات بعد.</div>
                            @endforelse
                        </div>
                    </div>

                    <!-- Add Comment -->
                    <div class="card mb-lg-0">
                        <div class="card-header">
                            <h3 class="card-title">اكتب تعليقك</h3>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('post.comment.store', $post->id) }}" method="post">
                                @csrf
                                <div class="form-group">
                                    <input type="text" name="author_name" class="form-control" placeholder="اسمك" required>
                                </div>
                                <div class="form-group">
                                    <input type="email" name="author_email" class="form-control" placeholder="بريدك الإلكتروني" required>
                                </div>
                                <div class="form-group">
                                    <textarea class="form-control" name="body" rows="6" placeholder="اكتب تعليقك هنا" required></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">إرسال</button>
                            </form>
                        </div>
                    </div>
                </div>
                <!--/Post Body-->
            </div>
        </div>
    </section>
@endsection
