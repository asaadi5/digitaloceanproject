@extends('front.layouts.master')

@section('main_content')
    <!--Breadcrumb-->
    <section>
        <div class="bannerimg cover-image bg-background3" data-bs-image-src="../assets/images/banners/banner2.jpg">
            <div class="header-text mb-0">
                <div class="container">
                    <div class="text-center text-white">
                        <h1 class="">لوحة التحكم الخاصة بي</h1>
                        <ol class="breadcrumb text-center">
                            <li class="breadcrumb-item"><a href="javascript:void(0);">الرئيسية</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">الطلبات</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--Breadcrumb-->

    <!--Orders-->
    <section class="sptb">
        <div class="container-fluid px-0">
            <div class="row  g-0">

                <!-- الشريط الجانبي  -->
                <div class="col-xl-2 col-lg-3 col-md-12">

                        @include('agent.sidebar.index')
                </div>
                <!-- /الشريط الجانبي  -->

                <!-- المحتوى  -->
                <div class="col-xl-10 col-lg-9 col-md-12">
                    <div class="card mb-0">
                        <div class="card-header">
                            <h3 class="card-title">جدول الطلبات</h3>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive userprof-tab">
                                <table class="table table-bordered table-hover mb-0 text-nowrap">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>رقم الفاتورة</th>
                                        <th>اسم الباقة</th>
                                        <th>السعر</th>
                                        <th>تاريخ الدفع</th>
                                        <th>تاريخ الانتهاء</th>
                                        <th>طريقة الدفع ومعرّف العملية</th>
                                        <th>الحالة</th>
                                        <th>طباعة الفاتورة</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>
                                            INV-1-1745432656
                                            <div><span class="badge badge-success">حاليًا نشط</span></div>
                                        </td>
                                        <td>Basic</td>
                                        <td>$9</td>
                                        <td>2025-04-23</td>
                                        <td>2025-05-08</td>
                                        <td>
                                            <strong>Stripe</strong><br>
                                            cs_test_a1G2l34wmcjYQpG3cQogzuzc7R8GLuPSQJ…<br>
                                            8FA6sBwiQGVNJt70UqGpYZm
                                        </td>
                                        <td><span class="badge badge-success">مكتمل</span></td>
                                        <td class="text-center">
                                            <a href="javascript:void(0);" class="btn btn-primary btn-sm text-white" title="طباعة">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>2</td>
                                        <td>INV-1-1745432556</td>
                                        <td>Basic</td>
                                        <td>$9</td>
                                        <td>2025-04-23</td>
                                        <td>2025-05-08</td>
                                        <td>
                                            <strong>Stripe</strong><br>
                                            cs_test_a1Q280yXjDNqqa5S5TGXSD2GuseyNKs0M0…<br>
                                            ibHRhD7caiFwRUMZhlHIAjPyD
                                        </td>
                                        <td><span class="badge badge-success">مكتمل</span></td>
                                        <td class="text-center">
                                            <a href="javascript:void(0);" class="btn btn-primary btn-sm text-white" title="طباعة">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>3</td>
                                        <td>INV-1-1745432201</td>
                                        <td>Basic</td>
                                        <td>$9</td>
                                        <td>2025-04-23</td>
                                        <td>2025-05-08</td>
                                        <td>
                                            <strong>Stripe</strong><br>
                                            cs_test_a1oMEOqZKsN8cMhnhwaDJH5dffPeTRtRY…<br>
                                            GzQjKGipdDrdDwabvGxlrGIJ7
                                        </td>
                                        <td><span class="badge badge-success">مكتمل</span></td>
                                        <td class="text-center">
                                            <a href="javascript:void(0);" class="btn btn-primary btn-sm text-white" title="طباعة">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>4</td>
                                        <td>INV-1-1745432127</td>
                                        <td>Gold</td>
                                        <td>$49</td>
                                        <td>2025-04-23</td>
                                        <td>2025-06-22</td>
                                        <td>
                                            <strong>PayPal</strong><br>
                                            02P885743D025641A
                                        </td>
                                        <td><span class="badge badge-success">مكتمل</span></td>
                                        <td class="text-center">
                                            <a href="javascript:void(0);" class="btn btn-primary btn-sm text-white" title="طباعة">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td>5</td>
                                        <td>INV-1-1745432026</td>
                                        <td>Basic</td>
                                        <td>$9</td>
                                        <td>2025-04-23</td>
                                        <td>2025-05-08</td>
                                        <td>
                                            <strong>PayPal</strong><br>
                                            0EU42667TS6353708
                                        </td>
                                        <td><span class="badge badge-success">مكتمل</span></td>
                                        <td class="text-center">
                                            <a href="javascript:void(0);" class="btn btn-primary btn-sm text-white" title="طباعة">
                                                <i class="fa fa-print"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
                <!-- /المحتوى -->

            </div>
        </div>
    </section>
    <!--/Orders-->
@endsection