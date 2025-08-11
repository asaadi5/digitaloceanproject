@extends('admin.layouts.master')

@section('main_content')
@include('admin.layouts.nav')
@include('admin.layouts.sidebar')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between">
            <h1>Edit Footer</h1>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('admin_setting_footer_update') }}" method="post">
                                @csrf
                                <div class="form-group mb-3">
                                    <label>Address</label>
                                    <input type="text" class="form-control" name="footer_address" value="{{ $setting->footer_address }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Email</label>
                                    <input type="text" class="form-control" name="footer_email" value="{{ $setting->footer_email }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Phone</label>
                                    <input type="text" class="form-control" name="footer_phone" value="{{ $setting->footer_phone }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Facebook</label>
                                    <input type="text" class="form-control" name="footer_facebook" value="{{ $setting->footer_facebook }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Twitter</label>
                                    <input type="text" class="form-control" name="footer_twitter" value="{{ $setting->footer_twitter }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label>LinkedIn</label>
                                    <input type="text" class="form-control" name="footer_linkedin" value="{{ $setting->footer_linkedin }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Instagram</label>
                                    <input type="text" class="form-control" name="footer_instagram" value="{{ $setting->footer_instagram }}">
                                </div>
                                <div class="form-group mb-3">
                                    <label>Copyright *</label>
                                    <input type="text" class="form-control" name="footer_copyright" value="{{ $setting->footer_copyright }}">
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Update</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection