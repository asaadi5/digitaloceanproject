<script src="{{ asset('dist-front/js/vendors/circle-progress.min.js') }}"></script>
<script src="{{ asset('dist-front/plugins/rating/jquery.rating-stars.js') }}"></script>
<script src="{{ asset('dist-front/plugins/counters/counterup.min.js') }}"></script>
<script src="{{ asset('dist-front/plugins/counters/numeric-counter.js') }}"></script>
<script src="{{ asset('dist-front/plugins/horizontal-menu/horizontal.js') }}"></script>
<script src="{{ asset('dist-front/js/jquery.touchSwipe.min.js') }}"></script>
<script src="{{ asset('dist-front/js/select2.js') }}"></script>
<script src="{{ asset('dist-front/js/sticky.js') }}"></script>
<script src="{{ asset('dist-front/plugins/cookie/jquery.ihavecookies.js') }}"></script>
<script src="{{ asset('dist-front/plugins/cookie/cookie.js') }}"></script>
<script src="{{ asset('dist-front/plugins/pscrollbar/pscrollbar.js') }}"></script>
<script src="{{ asset('dist-front/js/jquery.showmore.js') }}"></script>
<script src="{{ asset('dist-front/js/showmore.js') }}"></script>
<script src="{{ asset('dist-front/js/swipe.js') }}"></script>
<script src="{{ asset('dist-front/js/owl-carousel.js') }}"></script>
<script src="{{ asset('dist-front/js/themeColors.js') }}"></script>
<script src="{{ asset('dist-front/js/custom.js') }}"></script>
<script src="{{ asset('dist-front/js/custom-switcher.js') }}"></script>

@if($errors->any())
    @foreach($errors->all() as $error)
        <script>
            iziToast.error({
                message: '{{ $error }}',
                position: 'topLeft',
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
            position: 'topLeft',
            timeout: 5000,
            progressBarColor: '#00FF00',
        });
    </script>
@endif

@if(session('error'))
    <script>
        iziToast.error({
            message: '{{ session('error') }}',
            position: 'topLeft',
            timeout: 5000,
            progressBarColor: '#FF0000',
        });
    </script>
@endif