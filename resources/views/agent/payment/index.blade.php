@extends('front.layouts.master')

@section('main_content')
<div class="page-top" style="background-image: url({{ asset('uploads/'.$global_setting->banner) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Agent Payment</h2>
            </div>
        </div>
    </div>
</div>
<div class="page-content user-panel">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-md-12">
                <div class="card">
                    @include('agent.sidebar.index')
                </div>
            </div>
            <div class="col-lg-9 col-md-12">
                <h4>Current Plan</h4>

                @if($total_current_order > 0)
                <div class="row box-items mb-4">
                    <div class="col-md-4">
                        <div class="box1">
                            <h4>${{ $current_order->package->price }}</h4>
                            <p>{{ $current_order->package->name }}</p>
                            <p>({{ $days_left }} days remaining)</p>
                        </div>
                    </div>
                </div>
                @else 
                <div class="alert alert-danger mb_20">
                    You did not purchase any plan yet
                </div>
                @endif

                <h4>Upgrade Plan (Make Payment)</h4>
                <div class="table-responsive">
                    <table class="table table-bordered upgrade-plan-table">
                        <tr>
                            <td>
                                <form action="{{ route('agent_paypal') }}" method="post">
                                @csrf
                                <select name="package_id" class="form-control select2">
                                    @foreach($packages as $package)
                                        <option value="{{ $package->id }}">{{ $package->name }} (${{ $package->price }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <button type="submit" class="btn btn-secondary btn-sm buy-button">Pay with PayPal</button>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <form action="{{ route('agent_stripe') }}" method="post">
                                @csrf
                                <select name="package_id" class="form-control select2">
                                    @foreach($packages as $package)
                                        <option value="{{ $package->id }}">{{ $package->name }} (${{ $package->price }})</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <button type="submit" class="btn btn-secondary btn-sm buy-button">Pay with Stripe</button>
                                </form>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection