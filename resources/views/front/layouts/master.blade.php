<!DOCTYPE html>
<html lang="ar" >
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="wishlist-toggle-url" content="{{ route('wishlist_toggle') }}">
    <title>{{ env('APP_NAME') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('uploads/'.$global_setting->favicon) }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">

    @include('front.layouts.style')
    @include('front.layouts.script')
</head>
<body>

    @include('front.layouts.nav')

    @yield('main_content')

     @include('front.layouts.footer')

    <div class="scroll-top">
        <i class="fas fa-angle-up"></i>
    </div>

    @include('front.layouts.script_footer')
</body>
</html>
