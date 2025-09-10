<section class="main-footer">
    <footer class="bg-dark text-white">
        <div class="footer-main">
            <div class="container">
                <div class="row">
                    {{-- عن الموقع --}}
                    <div class="col-lg-3 col-md-12">
                        <h6>عن عقارات الشمال</h6>
                        <hr class="deep-purple accent-2 mb-4 mt-0 d-inline-block mx-auto">
                        <p>
                            {{ $global_setting->footer_about ?? 'عقارات الشمال هو موقع متخصص في عرض وبيع العقارات السكنية والتجارية في مختلف المناطق. هدفنا تسهيل عملية الشراء والبيع بأمان واحترافية.' }}
                        </p>
                        @if(!empty($global_setting->footer_about_2))
                            <p>{{ $global_setting->footer_about_2 }}</p>
                        @endif
                    </div>

                    {{-- روابط سريعة --}}
                    <div class="col-lg-2 col-sm-6">
                        <h6>روابط سريعة</h6>
                        <hr class="deep-purple text-primary accent-2 mb-4 mt-0 d-inline-block mx-auto">
                        <ul class="list-unstyled mb-0">
                            <li><a href="{{ route('home') }}" class="text-white">الرئيسية</a></li>
                            <li><a href="{{ route('contact') }}" class="text-white">اتصل بنا</a></li>
                            <li><a href="{{ route('about_us') }}" class="text-white">من نحن</a></li>
                            <li><a href="{{ route('pricing') }}" class="text-white">أسعار الإشتراكات</a></li>
                            <li><a href="{{ route('blog') }}" class="text-white">المدونة</a></li>
                            <li><a href="{{ route('agents') }}" class="text-white">الوكلاء</a></li>
                            {{-- (اختياري) خصّص روابط مباشرة للفئات الشائعة --}}
                            {{-- <li><a href="{{ route('properties.category',['category'=>'residential']) }}" class="text-white">السكني</a></li> --}}
                        </ul>
                    </div>

                    {{-- معلومات التواصل --}}
                    <div class="col-lg-3 col-sm-6">
                        <h6>معلومات التواصل</h6>
                        <hr class="deep-purple text-primary accent-2 mb-4 mt-0 d-inline-block mx-auto">

                        <ul class="list-unstyled mb-0">
                            @if(!empty($global_setting->footer_address))
                                <li>
                                    <span><i class="fa fa-home me-3 text-primary"></i>{{ $global_setting->footer_address }}</span>
                                </li>
                            @endif

                            @if(!empty($global_setting->footer_email))
                                <li>
                                    <a href="mailto:{{ $global_setting->footer_email }}">
                                        <i class="fa fa-envelope me-3 text-primary"></i>{{ $global_setting->footer_email }}
                                    </a>
                                </li>
                            @endif

                            @if(!empty($global_setting->footer_phone))
                                <li>
                                    <a href="tel:{{ preg_replace('/\s+/', '', $global_setting->footer_phone) }}">
                                        <i class="fa fa-phone me-3 text-primary"></i>{{ $global_setting->footer_phone }}
                                    </a>
                                </li>
                            @endif
                        </ul>

                        {{-- سوشال --}}
                        @if($global_setting->footer_facebook || $global_setting->footer_twitter || $global_setting->footer_instagram || $global_setting->footer_linkedin)
                            <ul class="list-unstyled list-inline mt-3">
                                @if($global_setting->footer_facebook)
                                    <li class="list-inline-item">
                                        <a href="{{ $global_setting->footer_facebook }}" target="_blank" class="btn-floating btn-sm rgba-white-slight mx-1 waves-effect waves-light">
                                            <i class="fa fa-facebook bg-facebook"></i>
                                        </a>
                                    </li>
                                @endif
                                @if($global_setting->footer_twitter)
                                    <li class="list-inline-item">
                                        <a href="{{ $global_setting->footer_twitter }}" target="_blank" class="btn-floating btn-sm rgba-white-slight mx-1 waves-effect waves-light">
                                            <i class="fa fa-twitter bg-info"></i>
                                        </a>
                                    </li>
                                @endif
                                @if($global_setting->footer_instagram)
                                    <li class="list-inline-item">
                                        <a href="{{ $global_setting->footer_instagram }}" target="_blank" class="btn-floating btn-sm rgba-white-slight mx-1 waves-effect waves-light">
                                            <i class="fa fa-instagram bg-danger"></i>
                                        </a>
                                    </li>
                                @endif
                                @if($global_setting->footer_linkedin)
                                    <li class="list-inline-item">
                                        <a href="{{ $global_setting->footer_linkedin }}" target="_blank" class="btn-floating btn-sm rgba-white-slight mx-1 waves-effect waves-light">
                                            <i class="fa fa-linkedin bg-linkedin"></i>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        @endif
                    </div>

                    {{-- النشرة البريدية + الدفع --}}
                    <div class="col-lg-4 col-md-12">
                        <h6>اشترك معنا</h6>
                        <hr class="deep-purple text-primary accent-2 mb-4 mt-0 d-inline-block mx-auto">
                        <div class="clearfix"></div>

                        <form action="{{ route('subscriber_send_email') }}" method="POST" class="form_subscribe_ajax">
                            @csrf
                            <div class="input-group w-100">
                                <input type="email" name="email" class="form-control br-tl-3 br-bl-3" placeholder="البريد الإلكتروني" required>
                                <button type="submit" class="btn btn-primary br-tr-3 br-br-3">اشترك</button>
                            </div>
                            <span class="text-danger error-text email_error"></span>
                        </form>

                        <h6 class="mb-0 mt-5">وسائل الدفع</h6>
                        <hr class="deep-purple text-primary accent-2 mb-2 mt-3 d-inline-block mx-auto">
                        <div class="clearfix"></div>
                        <ul class="footer-payments">
                            <li class="ps-0"><span><i class="fa fa-cc-amex text-muted" aria-hidden="true"></i></span></li>
                            <li><span><i class="fa fa-cc-visa text-muted" aria-hidden="true"></i></span></li>
                            <li><span><i class="fa fa-credit-card-alt text-muted" aria-hidden="true"></i></span></li>
                            <li><span><i class="fa fa-cc-mastercard text-muted" aria-hidden="true"></i></span></li>
                            <li><span><i class="fa fa-cc-paypal text-muted" aria-hidden="true"></i></span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- أسفل الفوتر --}}
        <div class="bg-dark text-white p-0">
            <div class="container">
                <div class="row d-flex">
                    <div class="col-lg-12 col-sm-12 mt-3 mb-3 text-center">
                        {{ $global_setting->footer_copyright ?: ('جميع الحقوق محفوظة © '.date('Y')) }}
                        <a href="{{ url('/') }}" class="fs-14 text-primary">عقارات الشمال</a>.
                        <span class="mx-2">|</span>
                        <a href="{{ route('terms') }}" class="text-white-50">شروط الاستخدام</a>
                        <span class="mx-1">•</span>
                        <a href="{{ route('privacy') }}" class="text-white-50">سياسة الخصوصية</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</section>

{{-- Ajax الاشتراك (نفس مبدأ القديم) --}}
<script>
    (function($){
        $(".form_subscribe_ajax").on('submit', function(e){
            e.preventDefault();
            $('#loader').show();
            var form = this;
            $.ajax({
                url:$(form).attr('action'),
                method:$(form).attr('method'),
                data:new FormData(form),
                processData:false,
                dataType:'json',
                contentType:false,
                beforeSend:function(){
                    $(form).find('span.error-text').text('');
                },
                success:function(data){
                    $('#loader').hide();
                    if(data.code == 0){
                        $.each(data.error_message, function(prefix, val) {
                            $(form).find('span.'+prefix+'_error').text(val[0]);
                        });
                    }else if(data.code == 1){
                        form.reset();
                        iziToast.success({
                            message: data.success_message,
                            position: 'topRight',
                            timeout: 5000,
                            progressBarColor: '#00FF00',
                        });
                    }
                },
                error:function(){
                    $('#loader').hide();
                    iziToast.error({
                        message: 'حدث خطأ غير متوقع. حاول مجددًا.',
                        position: 'topRight'
                    });
                }
            });
        });
    })(jQuery);
</script>
<div id="loader" style="display:none;"></div>
