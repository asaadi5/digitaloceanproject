@extends('front.layouts.master')

@section('main_content')
<div class="page-top" style="background-image: url({{ asset('uploads/'.$global_setting->banner) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Customer Wishlist</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-content user-panel">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-12">
                <div class="card">
                    @include('user.sidebar.index')
                </div>
            </div>
            <div class="col-lg-9 col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tbody>
                            <tr>
                                <th>SL</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Detail</th>
                                <th class="w-100">Action</th>
                            </tr>
                            @foreach ($wishlists as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ asset('uploads/'.$item->property->featured_photo) }}" alt="" class="w-200">
                                </td>
                                <td>{{ $item->property->name }}</td>
                                <td>${{ $item->property->price }}</td>
                                <td>
                                    <a href="{{ route('property_detail',$item->property->slug) }}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>
                                </td>
                                <td>
                                    <a href="{{ route('wishlist_delete',$item->id) }}" class="btn btn-danger btn-sm" onClick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection