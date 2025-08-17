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
            <div class="breadcrumb-title pe-3">لوحة التحكم</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0 align-items-center">
                        <li class="breadcrumb-item"><a href="javascript:;"><ion-icon name="home-outline"></ion-icon></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page"> الطلبات </li>
                    </ol>
                </nav>
            </div>
        </div>
        <!--end breadcrumb-->
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>الطلبات</h1>
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
                                            <th>الرقم التسلسلي</th>
                                            <th>رقم الطلب</th>
                                            <th>معلومات الوكيل</th>
                                            <th>اسم الباقة</th>
                                            <th>السعر</th>
                                            <th>تاريخ الدفع</th>
                                            <th>تاريخ الانتهاء</th>
                                            <th>طريقة الدفع & رقم العملية</th>
                                            <th>الحالة</th>
                                            <th>طباعة الفاتورة</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orders as $order)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                {{ $order->invoice_no }}<br>
                                                @if($order->currently_active == 1)
                                                <span class="badge bg-success">نشط حالياً</span>
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
    </div>
    <!-- end page content-->
</div>
<!--end page content wrapper-->
@endsection
