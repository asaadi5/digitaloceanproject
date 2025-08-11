@extends('admin.layouts.master')

@section('main_content')
@include('admin.layouts.nav')
@include('admin.layouts.sidebar')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>Orders</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered" id="example1">
                                    <thead>
                                        <tr>
                                            <th>SL</th>
                                            <th>Invoice Id</th>
                                            <th>Agent Info</th>
                                            <th>Package Name</th>
                                            <th>Price</th>
                                            <th>Payent Date</th>
                                            <th>Expire Date</th>
                                            <th>Payment Method & Transaction Id</th>
                                            <th>Status</th>
                                            <th>Print Invoice</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orders as $order)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{ $order->invoice_no }}<br>
                                                @if($order->currently_active == 1)
                                                <span class="badge bg-success">Currently Active</span>
                                                @endif
                                            </td>
                                            <td>
                                                <b>{{ $order->agent->name }}</b><br>
                                                {{ $order->agent->email }}
                                            </td>
                                            <td>{{ $order->package->name }}</td>
                                            <td>${{ $order->paid_amount }}</td>
                                            <td>{{ $order->purchase_date }}</td>
                                            <td>{{ $order->expire_date }}</td>
                                            <td style="word-wrap: break-word; word-break: break-all;">
                                                <b>{{ $order->payment_method }}</b><br>
                                                {{ $order->transaction_id }}
                                            </td>
                                            <td>
                                                @if($order->status == 'Completed')
                                                <span class="badge bg-success">{{ $order->status }}</span>
                                                @else
                                                <span class="badge bg-danger">{{ $order->status }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin_order_invoice',$order->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-print"></i></a>
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
        </div>
    </section>
</div>
@endsection