@extends('admin.layouts.master')

@section('main_content')
@include('admin.layouts.nav')
@include('admin.layouts.sidebar')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>Properties</h1>
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
                                            <th>Featured Photo</th>
                                            <th>Name</th>
                                            <th>Agent</th>
                                            <th>Location</th>
                                            <th>Type</th>
                                            <th>Purpose</th>
                                            <th>Price</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($properties as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>
                                                @if($item->featured_photo != null)
                                                <img src="{{ asset('uploads/'.$item->featured_photo) }}" alt="" class="w_100">
                                                @endif
                                            </td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->agent->name }}</td>
                                            <td>{{ $item->location->name }}</td>
                                            <td>{{ $item->type->name }}</td>
                                            <td>{{ $item->purpose }}</td>
                                            <td>${{ $item->price }}</td>
                                            <td>
                                                @if($item->status == 'Pending')
                                                <span class="badge bg-danger">{{ $item->status }}</span>
                                                @else
                                                <span class="badge bg-success">{{ $item->status }}</span>
                                                @endif
                                                <div><a href="{{ route('admin_property_change_status',$item->id) }}">Change</a></div>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin_property_detail',$item->id) }}" class="btn btn-primary btn-sm"><i class="fas fa-eye"></i></a>
                                                <a href="{{ route('admin_property_delete',$item->id) }}" class="btn btn-danger btn-sm" onClick="return confirm('Are you sure?');"><i class="fas fa-trash"></i></a>
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