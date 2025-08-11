@extends('admin.layouts.master')

@section('main_content')
@include('admin.layouts.nav')
@include('admin.layouts.sidebar')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>Amenities</h1>
            <div class="ml-auto">
                <a href="{{ route('admin_amenity_create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Add New</a>
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
                                            <th class="w_100">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($amenities as $amenity)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $amenity->name }}</td>
                                            <td>
                                                <a href="{{ route('admin_amenity_edit',$amenity->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-edit"></i></a>
                                                <a href="{{ route('admin_amenity_delete',$amenity->id) }}" class="btn btn-danger btn-sm" onClick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
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