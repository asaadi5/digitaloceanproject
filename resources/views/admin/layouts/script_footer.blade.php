<!-- JS Files -->
<script src="{{ asset('dist-main/js/jquery.min.js') }}"></script>
<script src="{{ asset('dist-main/plugins/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ asset('dist-main/plugins/metismenu/js/metisMenu.min.js') }}"></script>
<script src="{{ asset('dist-main/js/bootstrap.bundle.min.js') }}"></script>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<!-- plugins -->
<script src="{{ asset('dist-main/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('dist-main/plugins/apexcharts-bundle/js/apexcharts.min.js') }}"></script>
<script src="{{ asset('dist-main/plugins/chartjs/chart.min.js') }}"></script>

<!-- Page JS -->
<script src="{{ asset('dist-main/js/index.js') }}"></script>

<!-- Main JS -->
<script src="{{ asset('dist-main/js/main.js') }}"></script>

{{-- Custom Scripts --}}

@if($errors->any())
    @foreach($errors->all() as $error)
        <script>
            iziToast.error({
                message: '{{ $error }}',
                position: 'topRight',
                timeout: 5000,
                progressBarColor: '#FF0000',
            });
        </script>
    @endforeach
@endif

@if(session('success'))
    <script>
        iziToast.success({
            message: '{{ session('success') }}',
            position: 'topRight',
            timeout: 5000,
            progressBarColor: '#00FF00',
        });
    </script>
@endif

@if(session('error'))
    <script>
        iziToast.error({
            message: '{{ session('error') }}',
            position: 'topRight',
            timeout: 5000,
            progressBarColor: '#FF0000',
        });
    </script>
@endif
