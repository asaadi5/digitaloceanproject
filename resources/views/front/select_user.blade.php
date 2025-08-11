@extends('front.layouts.master')

@section('main_content')
<div class="page-top" style="background-image: url({{ asset('uploads/'.$global_setting->banner) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Select User</h2>
            </div>
        </div>
    </div>
</div>

<div class="page-content">
    <div class="container">
        <div class="row">
            <div class="col-xl-12 col-lg-12">
                <div class="main-select-user d-flex justify-content-center align-items-center">
                    <div class="select-customer">
                        <div><a href="{{ route('registration') }}">Customer Registration</a></div>
                        <div><a href="{{ route('login') }}">Customer Login</a></div>
                    </div>
                    <div class="select-agent">
                        <div><a href="{{ route('agent_registration') }}">Agent Registration</a></div>
                        <div><a href="{{ route('agent_login') }}">Agent Login</a></div>
                    </div>
                </div>  
            </div>
        </div>
    </div>
</div>
@endsection