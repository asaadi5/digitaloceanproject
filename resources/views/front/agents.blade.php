@extends('front.layouts.master')

@section('main_content')
    <!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3"
             data-bs-image-src="{{ asset('uploads/' . $global_setting->banner) }}">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="">الوكلاء</h1>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

    <!--Agents-->
    <section class="sptb bg-white">
        <div class="container">


            <div class="row">
                @forelse($agents as $a)
                    @php
                        // صورة
                        $img = $a->photo ? asset('uploads/'.$a->photo) : asset('uploads/default.png');

                        // عدد الإعلانات (مُحمّل مع withCount أو احتياطي سريع)
                        $propsCount = $a->properties_count
                            ?? \App\Models\Property::where('agent_id',$a->id)->where('status','active')->count();

                        // رابط صفحة الوكيل العامة
                        $showUrl = \Illuminate\Support\Facades\Route::has('agent')
                                    ? route('agent', $a->slug ?? $a->id)
                                    : url('agents/'.($a->slug ?? $a->id));
                    @endphp

                    <div class="col-xl-3 col-lg-6 col-md-6">
                        <div class="card mb-4">
                            <div class="item-card">
                                <div class="item-card-desc">
                                    <a href="{{ $showUrl }}"></a>
                                    <div class="item-card-img">
                                        <img src="{{ $img }}" alt="{{ $a->name }}" class="br-tr-7 br-tl-7">
                                    </div>
                                    <div class="item-card-text">
                                        <h4 class="mb-0">
                                            {{ $a->name }}
                                            @if(($a->status ?? 1) == 1)
                                                <i class="si si-check text-success fs-12 ms-1" title="موثّق"></i>
                                            @endif
                                        </h4>
                                        <span class="badge rounded-pill badge-primary w-15">
                                            {{ $propsCount }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning text-center mb-0">
                            لا يوجد وكلاء متاحون حالياً.
                        </div>
                    </div>
                @endforelse

                <div class="col-md-12 mt-4 d-flex justify-content-center">
                    {{ $agents->links() }}
                </div>
            </div>
        </div>
    </section>
    <!--/Agents-->
@endsection
