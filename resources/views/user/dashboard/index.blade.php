@extends('front.layouts.master')

@section('main_content')
<div class="page-top" style="background-image: url({{ asset('uploads/'.$global_setting->banner) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Customer Dashboard</h2>
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
                <h3>Hello, {{ Auth::guard('web')->user()->name }}</h3>
                <p>See all the statistics at a glance:</p>

                <div class="row box-items">
                    <div class="col-md-4">
                        <div class="box1">
                            <h4>{{ $total_messages }}</h4>
                            <p>Total Messages</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="box2">
                            <h4>{{ $total_wishlist_items }}</h4>
                            <p>Total Wishlist Items</p>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
@endsection