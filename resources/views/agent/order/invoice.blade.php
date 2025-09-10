@extends('front.layouts.master')

@section('main_content')
    <!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-bs-image-src="../assets/images/banners/banner2.jpg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white ">
                        <h1 class="">Invoice</h1>
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="javascript:void(0);">لوحة التحكم الخاصة بي</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">طباعة فاتورة</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--/Breadcrumb-->

    <!--Invoice-->
    <section class="sptb">
        <div class="container-fluid px-0">
            <div class="row  g-0">

                <!-- الشريط الجانبي  -->
                <div class="col-xl-2 col-lg-3 col-md-12">
                    @include('agent.sidebar.index')
                </div>
                <!-- /الشريط الجانبي  -->

                <!-- المحتوى  -->
                <div class="col-xl-8 col-lg-6 col-md-12">
                    <div class="card mb-0 ms-9">
                        <div class="card-header">
                            <h3 class="card-title mb-0">فاتورة</h3>
                        </div>

                        <div class="card-body" id="print_invoice">

                            <div class="d-flex justify-content-between align-items-center mb-5">

                                <div class="mb-0 order-lg-2">
                                    <img src="../assets/images/logo.png" alt="Logo" class="img-fluid align-middle" style="height:36px; width:auto;">
                                </div>


                                <div class="order-lg-1">
                                    <div>رقم الفاتورة: <strong>INV-1-1745432656</strong></div>
                                    <div>التاريخ: <strong>2025-04-23</strong></div>
                                </div>
                            </div>


                            <div class="row">

                                <div class="col-lg-6 order-lg-1 order-2 text-start ">
                                    <p class="h4 mb-2 fw-bold fa-5">من:</p>
                                    <address class="mb-0">
                                        عبد الرحمن بركات<br>
                                        admin@gmail.com<br>
                                        215-899-5780<br>
                                        3145 Glen Falls Road<br>
                                        Bensalem, PA 19020
                                    </address>
                                </div>

                                <div class="col-lg-6 order-lg-2 order-1 text-end ">
                                    <p class="h4 mb-2 fw-bold fa-5">إلى:</p>
                                    <address class="mb-0">
                                        محمد حسين<br>
                                        agent@gmail.com<br>
                                        (03) 5381 2166<br>
                                        48 Commercial Street,<br>
                                        Kyneton, Vic, Australia, 3444
                                    </address>
                                </div>
                            </div>


                            <div class="table-responsive push mt-5">
                                <table class="table table-bordered table-hover text-nowrap">
                                    <thead>
                                    <tr>
                                        <th class="text-center" style="width: 80px;">التسلسل</th>
                                        <th>اسم الباقة</th>
                                        <th class="text-center">سعر الباقة</th>
                                        <th class="text-center">تاريخ الشراء</th>
                                        <th class="text-center">تاريخ الانتهاء</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td class="text-center">1</td>
                                        <td>Basic</td>
                                        <td class="text-center">$9</td>
                                        <td class="text-center">2025-04-23</td>
                                        <td class="text-center">2025-12-08</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>


                            <div class="row mt-4">
                                <div class="col-lg-6">
                                    <p class="h5 mb-2">طريقة الدفع</p>
                                    <div>Stripe</div>
                                </div>
                                <div class="col-lg-6 text-end">
                                    <p class="h5 mb-2">الإجمالي</p>
                                    <div class="fs-16">$9</div>
                                </div>
                            </div>
                        </div>

                        <div class="card-footer d-flex justify-content-start">
                            <button type="button" class="btn btn-primary" onclick="window.print()">
                                <i class="icon icon-printer"></i> طباعة
                            </button>
                        </div>
                    </div>


                </div>
                <!-- /المحتوى -->

            </div>
        </div>
    </section>
    <!--/Invoice-->

                <script>
                    function printInvoice() {
                        let body = document.body.innerHTML;
                        let data = document.getElementById('print_invoice').innerHTML;
                        document.body.innerHTML = data;
                        window.print();
                        document.body.innerHTML = body;
                    }
                </script>
@endsection