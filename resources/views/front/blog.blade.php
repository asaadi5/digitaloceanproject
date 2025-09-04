@extends('front.layouts.master')

@section('main_content')
    <!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-bs-image-src="../assets/images/banners/banner2.jpg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="">Blog-List</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

    <!--Add listing-->
    <section class="sptb">
        <div class="container">
            <div class="row">
                <!--Left Side Content-->
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


                    {{-- Categories (ديناميكي بنفس الشكل) --}}
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Categories</h3>
                        </div>
                        <div class="card-body p-0">
                            <div class="list-catergory">
                                <div class="item-list">
                                    <ul class="list-group mb-0">
                                        @php
                                            // لتكرار نفس شكل الأيقونات والألوان بالتناوب فقط للحفاظ على الشكل
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

                                        @foreach($types as $k => $type)
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

                    {{-- Popular Tags (ثابت كما هو) --}}
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Popular Tags</h3>
                        </div>
                        <div class="card-body">
                            <div class="product-tags clearfix">
                                <ul class="list-unstyled mb-0">
                                    <li><a href="javascript:void(0);">RealEstate</a></li>
                                    <li><a href="javascript:void(0);">Homes</a></li>
                                    <li><a href="javascript:void(0);">3BHK Flatss</a></li>
                                    <li><a href="javascript:void(0);">2BHK Homes</a></li>
                                    <li><a href="javascript:void(0);">Luxury Rooms</a></li>
                                    <li><a href="javascript:void(0);">Apartments</a></li>
                                    <li><a href="javascript:void(0);">3BHK Flatss</a></li>
                                    <li><a href="javascript:void(0);">Homes</a></li>
                                    <li><a href="javascript:void(0);">Luxury House For Sale</a></li>
                                    <li><a href="javascript:void(0);">Apartments</a></li>
                                    <li><a href="javascript:void(0);" class="mb-0">Luxury Rooms</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <!--/Left Side Content-->

                <div class="col-xl-8 col-lg-8 col-md-12">
                    <!--Add lists-->
                    <div class="row">
                        @foreach($posts as $post)
                            @php
                                $img = $post->photo ? asset('uploads/'.ltrim($post->photo,'/')) : asset('assets/images/products/h3.png');
                                $badgeClass = 'badge-info'; // نحافظ على نفس الشكل (لون البادج)
                                $commentsCount = $post->comments_count ?? 0;
                            @endphp

                            <div class="col-xl-12 col-lg-12 col-md-12">
                                <div class="card overflow-hidden">
                                    <div class="row no-gutters blog-list">
                                        <div class="col-xl-4 col-lg-12 col-md-12">
                                            <div class="item7-card-img">
                                                <a href="{{ route('post', $post->slug) }}"></a>
                                                <img src="{{ $img }}" alt="img" class="cover-image">
                                                <div class="item7-card-text">
                                                    <span class="badge {{ $badgeClass }}">{{ optional($post->type)->name ?? 'Category' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xl-8 col-lg-12 col-md-12">
                                            <div class="card-body">
                                                <div class="item7-card-desc d-flex mb-1">
                                                    <a href="javascript:void(0);" class="text-muted">
                                                        <i class="fa fa-calendar-o text-muted me-2"></i>{{ optional($post->created_at)->format('M-d-Y') }}
                                                    </a>
                                                    <div class="ms-auto">
                                                        <a href="javascript:void(0);" class="text-muted">
                                                            <i class="fa fa-comment-o text-muted me-2"></i>{{ $commentsCount }} Comments
                                                        </a>
                                                    </div>
                                                </div>
                                                <a href="{{ route('post', $post->slug) }}" class="text-dark">
                                                    <h4 class="font-weight-semibold mb-4">{{ $post->title }}</h4>
                                                </a>
                                                <p class="">{{ \Illuminate\Support\Str::limit($post->short_description, 150) }}</p>
                                                <a href="{{ route('post', $post->slug) }}" class="btn btn-primary btn-sm">Read More<i class="fe fe-chevrons-right ms-1"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="center-block text-center">
                        {{-- روابط الصفحات (تظهر بنفس المكان/المحاذاة) --}}
                        {{ $posts->links() }}
                    </div>
                    <!--/Add lists-->
                </div>
            </div>
        </div>
    </section>
    <!--/Add Listing-->
@endsection
