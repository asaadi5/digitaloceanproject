@extends('front.layouts.master')

@section('main_content')
<div class="page-top" style="background-image: url({{ asset('uploads/'.$global_setting->banner) }})">
    <div class="bg"></div>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Agent Create Property</h2>
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
                <form action="{{ route('agent_property_store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="col-md-4 mb-3">
                        <label for="" class="form-label">Featured Photo *</label>
                        <input type="file" name="featured_photo">
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="" class="form-label">Name *</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="" class="form-label">Slug *</label>
                            <input type="text" name="slug" class="form-control" value="{{ old('slug') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="" class="form-label">Price *</label>
                            <input type="text" name="price" class="form-control" value="{{ old('price') }}">
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="" class="form-label">Description</label>
                            <textarea name="description" class="form-control editor" cols="30" rows="10">{{ old('description') }}</textarea>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="" class="form-label">Location *</label>
                            <select name="location_id" class="form-control select2">
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="" class="form-label">Type *</label>
                            <select name="type_id" class="form-control select2">
                                @foreach($types as $type)
                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="" class="form-label">Purpose *</label>
                            <select name="purpose" class="form-control select2">
                                <option value="Sale">Sale</option>
                                <option value="Rent">Rent</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="" class="form-label">Bedrooms *</label>
                            <input type="number" name="bedroom" class="form-control" value="{{ old('bedroom') }}" min="0" max="100">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="" class="form-label">Bathrooms *</label>
                            <input type="number" name="bathroom" class="form-control" value="{{ old('bathroom') }}" min="0" max="100">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="" class="form-label">Size (Sqft) *</label>
                            <input type="number" name="size" class="form-control" value="{{ old('size') }}" min="0" max="100000">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="" class="form-label">Floor</label>
                            <input type="number" name="floor" class="form-control" value="{{ old('floor') }}" min="0" max="100">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="" class="form-label">Garage</label>
                            <input type="number" name="garage" class="form-control" value="{{ old('garage') }}" min="0" max="100">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="" class="form-label">Balcony</label>
                            <input type="number" name="balcony" class="form-control" value="{{ old('balcony') }}" min="0" max="100">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="" class="form-label">Address *</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="" class="form-label">Built Year</label>
                            <input type="number" name="built_year" class="form-control" value="{{ old('built_year') }}" min="1900" max="{{ date('Y') }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="" class="form-label">Is Featured?</label>
                            <select name="is_featured" class="form-control select2">
                                <option value="Yes" {{ old('is_featured') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                <option value="No" {{ old('is_featured') == 'No' ? 'selected' : '' }}>No</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="" class="form-label">Location Map</label>
                            <textarea name="map" class="form-control h-150" cols="30" rows="10">{{ old('map') }}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="" class="form-label">Amenities</label>
                            <div class="row">
                                @foreach($amenities as $amenity)
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <input name="amenity[]" class="form-check-input" type="checkbox" value="{{ $amenity->id }}" id="chk{{ $loop->iteration }}">
                                        <label class="form-check-label" for="chk{{ $loop->iteration }}">
                                            {{ $amenity->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-12 mb-3">
                            <input type="submit" class="btn btn-primary" value="Submit">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection