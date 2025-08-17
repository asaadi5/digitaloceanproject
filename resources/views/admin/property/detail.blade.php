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
                <div class="breadcrumb-title pe-3"> قسم العقارات </div>
                <div class="ps-3">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0 p-0 align-items-center">
                            <li class="breadcrumb-item"><a href="javascript:;"><ion-icon name="home-outline"></ion-icon></a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page"> العقارات  </li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!--end breadcrumb-->
            <div class="main-content">
                <div class="section-header d-flex justify-content-between">
                    <h1> تفاصيل العقار -  {{ $property->name }} </h1>
                </div>
                <div class="section-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <tr>
                                                <th class="table-img"> الصورة </th>
                                                <td>
                                                    <img src="{{ asset('uploads/'.$property->featured_photo) }}" alt="" class="w_200">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>الاسم</th>
                                                <td>{{ $property->name }}</td>
                                            </tr>
                                            <tr>
                                                <th> المعرف النصي </th>
                                                <td>{{ $property->slug }}</td>
                                            </tr>
                                            <tr>
                                                <th> الوصف </th>
                                                <td>{!! $property->description !!}</td>
                                            </tr>
                                            <tr>
                                                <th> السعر </th>
                                                <td>${{ $property->price }}</td>
                                            </tr>
                                            <tr>
                                                <th> الوكيل </th>
                                                <td>{{ $property->agent->name }}</td>
                                            </tr>
                                            <tr>
                                                <th> الموقع </th>
                                                <td>{{ $property->location->name }}</td>
                                            </tr>
                                            <tr>
                                                <th> النوع </th>
                                                <td>{{ $property->type->name }}</td>
                                            </tr>
                                            <tr>
                                                <th> وسائل الراحة </th>
                                                <td>
                                                    @php
                                                        $amenity_arr = explode(',', $property->amenities);
                                                        $amenity = \App\Models\Amenity::whereIn('id', $amenity_arr)->get();
                                                        foreach($amenity as $item) {
                                                            echo '<span class="badge bg-primary me-1">'.$item->name.'</span>';
                                                        }
                                                    @endphp
                                                </td>
                                            </tr>
                                            <tr>
                                                <th> الغرض </th>
                                                <td>{{ $property->purpose }}</td>
                                            </tr>
                                            <tr>
                                                <th> غرف النوم </th>
                                                <td>{{ $property->bedroom }}</td>
                                            </tr>
                                            <tr>
                                                <th> الحمامات </th>
                                                <td>{{ $property->bathroom }}</td>
                                            </tr>
                                            <tr>
                                                <th> مساحة البناء </th>
                                                <td>{{ $property->size }} متر مربع</td>
                                            </tr>
                                            <tr>
                                                <th> مساحة الطابق </th>
                                                <td>{{ $property->floor }} متر مربع</td>
                                            </tr>
                                            <tr>
                                                <th> المرآب </th>
                                                <td>{{ $property->garage }}</td>
                                            </tr>
                                            <tr>
                                                <th> الشرف </th>
                                                <td>{{ $property->balcony }}</td>
                                            </tr>
                                            <tr>
                                                <th> العنوان </th>
                                                <td>{{ $property->address }}</td>
                                            </tr>
                                            <tr>
                                                <th> سنة البناء </th>
                                                <td>{{ $property->built_year }}</td>
                                            </tr>
                                            <tr>
                                                <th> الموقع على الخريطة </th>
                                                <td>{!! $property->map !!}</td>
                                            </tr>
                                            <tr>
                                                <th> مميز؟ </th>
                                                <td>{{ $property->is_featured }}</td>
                                            </tr>
                                            <tr>
                                                <th> صور العقار </th>
                                                <td>
                                                    @foreach($property->photos as $item)
                                                        <img src="{{ asset('uploads/'.$item->photo) }}" alt="" class="w_200">
                                                    @endforeach
                                                </td>
                                            </tr>
                                            <tr>
                                                <th> فيديو للعقار </th>
                                                <td>
                                                    @foreach($property->videos as $item)
                                                        <iframe width="560" height="315" src="https://www.youtube.com/embed/{{ $item->video }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                                    @endforeach
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- end page content-->
    </div>
    <!--end page content wrapper-->
@endsection
