@extends('admin.layouts.master')

@section('main_content')
@include('admin.layouts.nav')
@include('admin.layouts.sidebar')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>Customers</h1>
            <div class="ml-auto">
                <a href="{{ route('admin_customer_create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add New</a>
            </div>
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
                                            <th>Photo</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Status</th>
                                            <th class="w_100">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($customers as $customer)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if($customer->photo == null)
                                                <img src="{{ asset('uploads/default.png') }}" alt="" class="w_100">
                                                @else
                                                <img src="{{ asset('uploads/'.$customer->photo) }}" alt="" class="w_100">
                                                @endif
                                            </td>
                                            <td>{{ $customer->name }}</td>
                                            <td>{{ $customer->email }}</td>
                                            <td>
                                                @if($customer->status == 0)
                                                <span class="badge bg-warning">Pending</span>
                                                @elseif($customer->status == 1)
                                                <span class="badge bg-success">Active</span>
                                                @else
                                                <span class="badge bg-danger">Suspended</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin_customer_edit',$customer->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                                <a href="{{ route('admin_customer_delete',$customer->id) }}" class="btn btn-danger btn-sm" onClick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
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