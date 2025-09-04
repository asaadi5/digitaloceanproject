@extends('front.layouts.master')

@section('main_content')
<div class="page-top" style="background-image: url({{ asset('uploads/'.$global_setting->banner) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Invoice No: {{ $order->invoice_no }}</h2>
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
                <div class="invoice-container" id="print_invoice">
                    <div class="invoice-top">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-border-0">
                                        <tbody>
                                            <tr>
                                                <td class="w-50">
                                                    <img src="{{ asset('uploads/'.$global_setting->logo) }}" alt="" class="w-100">
                                                </td>
                                                <td class="w-50">
                                                    <div class="invoice-top-right">
                                                        <h4>Invoice</h4>
                                                        <h5>Invoice No: {{ $order->invoice_no }}</h5>
                                                        <h5>Date: {{ $order->purchase_date }}</h5>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="invoice-middle">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-border-0">
                                        <tbody>
                                            <tr>
                                                <td class="w-50">
                                                    <div class="invoice-middle-left">
                                                        <h4>Invoice To:</h4>
                                                        <p class="mb_0">{{ $order->agent->name }}</p>
                                                        <p class="mb_0">{{ $order->agent->email }}</p>
                                                        <p class="mb_0">{{ $order->agent->phone }}</p>
                                                        <p class="mb_0">{{ $order->agent->address }}</p>
                                                        <p class="mb_0">{{ $order->agent->city }}, {{ $order->agent->state }}, {{ $order->agent->country }}, {{ $order->agent->zip }}</p>
                                                    </div>
                                                </td>
                                                <td class="w-50">
                                                    <div class="invoice-middle-right">
                                                        <h4>Invoice From:</h4>
                                                        <p class="mb_0">{{ env('APP_NAME') }}</p>
                                                        @php
                                                        $admin = App\Models\Admin::where('id',1)->first();
                                                        @endphp
                                                        <p class="mb_0 color_6d6d6d">{{ $admin->email }}</p>
                                                        <p class="mb_0">215-899-5780</p>
                                                        <p class="mb_0">3145 Glen Falls Road</p>
                                                        <p class="mb_0">Bensalem, PA 19020</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="invoice-item">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered invoice-item-table">
                                        <tbody>
                                            <tr>
                                                <th>SL</th>
                                                <th>Package Name</th>
                                                <th>Package Price</th>
                                                <th>Purchase Date</th>
                                                <th>Expire Date</th>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td>{{ $order->package->name }}</td>
                                                <td>${{ $order->paid_amount }}</td>
                                                <td>{{ $order->purchase_date }}</td>
                                                <td>{{ $order->expire_date }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="invoice-bottom">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-border-0">
                                        <tbody>
                                            <td class="w-70 invoice-bottom-payment">
                                                <h4>Payment Method</h4>
                                                <p>{{ $order->payment_method }}</p>
                                            </td>
                                            <td class="w-30 tar invoice-bottom-total-box">
                                                <h4>Total</h4>
                                                <p>${{ $order->paid_amount }}</p>
                                            </td>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="print-button mt_25">
                    <a onclick="printInvoice()" href="javascript:;" class="btn btn-primary"><i class="fas fa-print"></i> Print</a>
                </div>
                <script>
                    function printInvoice() {
                        let body = document.body.innerHTML;
                        let data = document.getElementById('print_invoice').innerHTML;
                        document.body.innerHTML = data;
                        window.print();
                        document.body.innerHTML = body;
                    }
                </script>
            </div>
        </div>
    </div>
</div>
@endsection