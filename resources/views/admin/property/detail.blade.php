@extends('admin.layouts.master')

@section('main_content')
@include('admin.layouts.nav')
@include('admin.layouts.sidebar')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>Property Detail - {{ $property->name }}</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <tr>
                                        <th class="w_200">Featured Photo</th>
                                        <td>
                                            <img src="{{ asset('uploads/'.$property->featured_photo) }}" alt="" class="w_200">
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Name</th>
                                        <td>{{ $property->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Slug</th>
                                        <td>{{ $property->slug }}</td>
                                    </tr>
                                    <tr>
                                        <th>Description</th>
                                        <td>{!! $property->description !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Price</th>
                                        <td>${{ $property->price }}</td>
                                    </tr>
                                    <tr>
                                        <th>Agent</th>
                                        <td>{{ $property->agent->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Location</th>
                                        <td>{{ $property->location->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Type</th>
                                        <td>{{ $property->type->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>Amenities</th>
                                        <td>
                                            @php
                                            $amenity_arr = explode(',', $property->amenities);
                                            $amenity = \App\Models\Amenity::whereIn('id', $amenity_arr)->get();
                                            foreach($amenity as $item) {
                                                echo '<span class="badge bg-primary me-1">'.$item->name.'</span>';
                                            }
                                            @endphp
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Purpose</th>
                                        <td>{{ $property->purpose }}</td>
                                    </tr>
                                    <tr>
                                        <th>Bedrooms</th>
                                        <td>{{ $property->bedroom }}</td>
                                    </tr>
                                    <tr>
                                        <th>Bathrooms</th>
                                        <td>{{ $property->bathroom }}</td>
                                    </tr>
                                    <tr>
                                        <th>Size</th>
                                        <td>{{ $property->size }} sqft</td>
                                    </tr>
                                    <tr>
                                        <th>Floor</th>
                                        <td>{{ $property->floor }} sqft</td>
                                    </tr>
                                    <tr>
                                        <th>Garage</th>
                                        <td>{{ $property->garage }}</td>
                                    </tr>
                                    <tr>
                                        <th>Balcony</th>
                                        <td>{{ $property->balcony }}</td>
                                    </tr>
                                    <tr>
                                        <th>Address</th>
                                        <td>{{ $property->address }}</td>
                                    </tr>
                                    <tr>
                                        <th>Built Year</th>
                                        <td>{{ $property->built_year }}</td>
                                    </tr>
                                    <tr>
                                        <th>Map</th>
                                        <td>{!! $property->map !!}</td>
                                    </tr>
                                    <tr>
                                        <th>Is Featured?</th>
                                        <td>{{ $property->is_featured }}</td>
                                    </tr>
                                    <tr>
                                        <th>Photo Gallery</th>
                                        <td>
                                            @foreach($property->photos as $item)
                                            <img src="{{ asset('uploads/'.$item->photo) }}" alt="" class="w_200">
                                            @endforeach
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Video Gallery</th>
                                        <td>
                                            @foreach($property->videos as $item)
                                            <iframe width="560" height="315" src="https://www.youtube.com/embed/{{ $item->video }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                            @endforeach
                                        </td>
                                    </tr>
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