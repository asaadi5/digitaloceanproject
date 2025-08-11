@extends('admin.layouts.master')

@section('main_content')
@include('admin.layouts.nav')
@include('admin.layouts.sidebar')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>Packages</h1>
            <div class="ml-auto">
                <a href="{{ route('admin_package_create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add New</a>
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
                                            <th>Name</th>
                                            <th>Price</th>
                                            <th>Allowed Days</th>
                                            <th>Allowed Properties</th>
                                            <th>Allowed Featured Properties</th>
                                            <th>Allowed Photos</th>
                                            <th>Allowed Videos</th>
                                            <th class="w_100">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($packages as $package)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $package->name }}</td>
                                            <td>${{ $package->price }}</td>
                                            <td>
                                                @if($package->allowed_days != -1)
                                                {{ $package->allowed_days }}
                                                @else
                                                Unlimited
                                                @endif
                                            </td>
                                            <td>
                                                @if($package->allowed_properties != -1)
                                                {{ $package->allowed_properties }}
                                                @else
                                                Unlimited
                                                @endif
                                            </td>
                                            <td>
                                                @if($package->allowed_featured_properties != -1)
                                                {{ $package->allowed_featured_properties }}
                                                @else
                                                Unlimited
                                                @endif
                                            </td>
                                            <td>
                                                @if($package->allowed_photos != -1)
                                                {{ $package->allowed_photos }}
                                                @else
                                                Unlimited
                                                @endif
                                            </td>
                                            <td>
                                                @if($package->allowed_videos != -1)
                                                {{ $package->allowed_videos }}
                                                @else
                                                Unlimited
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin_package_edit',$package->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                                <a href="{{ route('admin_package_delete',$package->id) }}" class="btn btn-danger btn-sm" onClick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
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