<!DOCTYPE html>
<html lang="ar" dir="rtl" class="semi-dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Panel</title>

    <link rel="icon" type="image/png" href="{{ asset('uploads/'.$global_setting->favicon) }}">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">

    @include('admin.layouts.style')
    @include('admin.layouts.script')
</head>
<body>
<div class="wrapper">
    @yield('main_content')

    </div> <!-- End of wrapper -->


@include('admin.layouts.script_footer')

</body>
</html>
