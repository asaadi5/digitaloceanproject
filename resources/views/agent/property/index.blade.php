@extends('front.layouts.master')

@section('main_content')
<div class="page-top" style="background-image: url({{ asset('uploads/'.$global_setting->banner) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Agent Properties</h2>
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
                <div class="table-responsive">
                    <table class="table table-bordered" id="datatable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Featured Photo</th>
                                <th>Name</th>
                                <th>Agent</th>
                                <th>Location</th>
                                <th>Type</th>
                                <th>Purpose</th>
                                <th>Is Featured?</th>
                                <th>Status</th>
                                <th class="w-150">Options</th>
                                <th class="w-100">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($properties as $property)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    <img src="{{ asset('uploads/' . $property->featured_photo) }}" alt="" class="w-100">
                                </td>
                                <td>{{ $property->name }}</td>
                                <td>{{ $property->agent->name }}</td>
                                <td>{{ $property->location->name }}</td>
                                <td>{{ $property->type->name }}</td>
                                <td>{{ $property->purpose }}</td>
                                <td>
                                    @if($property->is_featured == 'Yes')
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-danger">No</span>
                                    @endif
                                </td>
                                <td>
                                    @if($property->status == 'Active')
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('agent_property_photo_gallery',$property->id) }}" class="btn btn-primary btn-sm btn-sm-custom w-100-p mb_5">Photo Gallery</a>
                                    <a href="{{ route('agent_property_video_gallery',$property->id) }}" class="btn btn-primary btn-sm btn-sm-custom w-100-p mb_5">Video Gallery</a>
                                </td>
                                <td>
                                    <a href="{{ route('agent_property_edit', $property->id) }}" class="btn btn-warning btn-sm text-white"><i class="fas fa-edit"></i></a>
                                    <a href="{{ route('agent_property_delete', $property->id) }}" class="btn btn-danger btn-sm" onClick="return confirm('Are you sure?');"><i class="fas fa-trash-alt"></i></a>
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
@endsection