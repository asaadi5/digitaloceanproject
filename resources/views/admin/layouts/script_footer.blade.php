<!-- JS Files -->
<script src="{{ asset('dist-main/plugins/simplebar/js/simplebar.min.js') }}"></script>
<script src="{{ asset('dist-main/plugins/metismenu/js/metisMenu.min.js') }}"></script>
<script src="{{ asset('dist-main/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('dist-main/js/FullScreenToggler.js') }}"></script>
<script src="{{ asset('dist-main/js/sweetalert2.all.min.js') }}"></script>

<!-- Ionicons -->
<script type="module" src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.esm.js"></script>
<script nomodule src="https://unpkg.com/ionicons@5.5.2/dist/ionicons/ionicons.js"></script>

<!-- plugins -->
<script src="{{ asset('dist-main/plugins/perfect-scrollbar/js/perfect-scrollbar.js') }}"></script>
<script src="{{ asset('dist-main/plugins/apexcharts-bundle/js/apexcharts.min.js') }}"></script>
<script src="{{ asset('dist-main/plugins/chartjs/chart.min.js') }}"></script>
<script src="{{ asset('dist-main/plugins/fontawesome-free-7.0.0-web/js/all.min.js') }}"></script>



<!-- Page JS -->
<script src="{{ asset('dist-main/js/index.js') }}"></script>

<!-- Main JS -->
<script src="{{ asset('dist-main/js/main.js') }}"></script>

{{-- Custom Scripts --}}
<script>
    (function($){
        "use strict";
        //$(".inputtags").tagsinput('items');
        $(document).ready(function() {
            $('#example1').DataTable({
                dom:
                    '<"row mb-2 align-items-center"' +
                    '<"col-12 col-md-6 d-flex justify-content-md-start" f>' +
                    '<"col-12 col-md-6 d-flex justify-content-md-end"   l>' +
                    '>' +
                    't' +
                    '<"row mt-2 align-items-center"' +
                    '<"col-12 col-md-6" i>' +
                    '<"col-12 col-md-6 d-flex justify-content-md-end" p>' +
                    '>',
                language: {
                    search: "بحث:",
                    lengthMenu: "عرض _MENU_ نتيجة",
                    info: "إظهار _START_ إلى _END_ من أصل _TOTAL_ نتيجة",
                    infoEmpty: "لا توجد نتائج",
                    infoFiltered: "(منتقاة من إجمالي _MAX_ نتيجة)",
                    zeroRecords: "لا نتائج مطابقة",
                    loadingRecords: "جارٍ التحميل...",
                    processing: "جارٍ المعالجة...",
                    paginate: { first: "الأولى", previous: "السابق", next: "التالي", last: "الأخيرة" },
                    aria: { sortAscending: ": ترتيب تصاعدي", sortDescending: ": ترتيب تنازلي" }
                }
            });

            //Photo Name
            const input = document.getElementById('dash_photo');
            const nameEl = document.getElementById('dash_photoName');

            input.addEventListener('change', () => {
                if (input.files && input.files.length) {
                    let label = input.multiple
                        ? Array.from(input.files).map(f => f.name).join(', ')
                        : input.files[0].name;

                    const MAX = 45; // للحد من الطول بالعرض الضيق
                    if (label.length > MAX) label = label.slice(0, MAX - 1) + '…';

                    nameEl.textContent = label;
                    nameEl.classList.remove('text-muted');
                } else {
                    nameEl.textContent = 'لم يتم اختيار ملف';
                    nameEl.classList.add('text-muted');
                }
            });

        });



        //sweetalert2
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.delete-btn');
            if (!btn) return;

            e.preventDefault(); //
            Swal.fire({
                title: 'هل أنت متأكد من الحذف؟',
                text: 'لا يمكن التراجع عن هذه العملية!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم',
                cancelButtonText: 'إلغاء',
                reverseButtons: true,  //
                focusCancel: true
            }).then((res) => {
                if (res.isConfirmed) {
                    window.location.href = btn.getAttribute('href');
                }
            });
        });
        //اذا نجح الحذف
        /*
        Swal.fire({
            toast: true, position: 'top-end',
            icon: 'success', title: 'تم الحذف بنجاح',
            showConfirmButton: false, timer: 1500, timerProgressBar: true
        });

         */

    })(jQuery);
</script>

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
