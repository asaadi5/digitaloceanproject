@extends('front.layouts.master')

@section('main_content')
<div class="page-top" style="background-image: url({{ asset('uploads/'.$global_setting->banner) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Agents</h2>
            </div>
        </div>
    </div>
</div>
<div class="agent pb_40">
    <div class="container">
        <div class="row">
            @foreach($agents as $item)
            <div class="col-lg-3 col-md-3">
                <div class="item">
                    <div class="photo">
                        <a href="{{ route('agent',$item->id) }}"><img src="{{ asset('uploads/'.$item->photo) }}" alt=""></a>
                    </div>
                    <div class="text">
                        <h2>
                            <a href="{{ route('agent',$item->id) }}">{{ $item->name }}</a>
                        </h2>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="col-md-12">
                {{ $agents->links() }}
            </div>
        </div>
    </div>
</div>
@endsection