<section class="main-footer">
    <footer class="bg-dark text-white">
        <div class="footer-main">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3 col-md-12">
                        <h6>عن عقارات الشمال</h6>
                        <hr class="deep-purple accent-2 mb-4 mt-0 d-inline-block mx-auto">
                        <p>
                            عقارات الشمال هو موقع متخصص في عرض وبيع العقارات السكنية والتجارية في مختلف المناطق السورية. هدفنا هو تسهيل عملية الشراء والبيع بأمان واحترافية.
                        </p>
                        <p>
                            نساعدك في العثور على منزل أحلامك أو بيع عقارك بأفضل طريقة ممكنة.
                        </p>
                    </div>

                    <div class="col-lg-2 col-sm-6">
                        <h6>روابط سريعة</h6>
                        <hr class="deep-purple text-primary accent-2 mb-4 mt-0 d-inline-block mx-auto">
                        <ul class="list-unstyled mb-0">
                            <li><a href="{{ route('login_submit') }}" class="text-white">فريقنا</a></li>
                            <li><a href="{{ route('login_submit') }}" class="text-white">اتصل بنا</a></li>
                            <li><a href="{{ route('login_submit') }}" class="text-white">حول الموقع</a></li>
                            <li><a href="{{ route('login_submit') }}" class="text-white">عقارات فاخرة</a></li>
                            <li><a href="{{ route('login_submit') }}" class="text-white">المدونة</a></li>
                            <li><a href="{{ route('login_submit') }}" class="text-white">الشروط والأحكام</a></li>
                        </ul>
                    </div>

                    <div class="col-lg-3 col-sm-6">
                        <h6>معلومات التواصل</h6>
                        <hr class="deep-purple text-primary accent-2 mb-4 mt-0 d-inline-block mx-auto">
                        <ul class="list-unstyled mb-0">
                            <li>
                                <a href="#"><i class="fa fa-home me-3 text-primary"></i> حلب - سوريا</a>
                            </li>
                            <li>
                                <a href="mailto:info@northrealestate.com"><i class="fa fa-envelope me-3 text-primary"></i> info@northrealestate.com</a>
                            </li>
                            <li>
                                <a href="tel:+963944123456"><i class="fa fa-phone me-3 text-primary"></i> +963 944 123 456</a>
                            </li>
                            <li>
                                <a href="#"><i class="fa fa-print me-3 text-primary"></i> +963 21 456 789</a>
                            </li>
                        </ul>
                        <ul class="list-unstyled list-inline mt-3">
                            <li class="list-inline-item">
                                <a href="https://facebook.com/propertyfinder" target="_blank" class="btn-floating btn-sm rgba-white-slight mx-1 waves-effect waves-light">
                                    <i class="fa fa-facebook bg-facebook"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://twitter.com/zillow" target="_blank" class="btn-floating btn-sm rgba-white-slight mx-1 waves-effect waves-light">
                                    <i class="fa fa-twitter bg-info"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://plus.google.com" target="_blank" class="btn-floating btn-sm rgba-white-slight mx-1 waves-effect waves-light">
                                    <i class="fa fa-google-plus bg-danger"></i>
                                </a>
                            </li>
                            <li class="list-inline-item">
                                <a href="https://linkedin.com" target="_blank" class="btn-floating btn-sm rgba-white-slight mx-1 waves-effect waves-light">
                                    <i class="fa fa-linkedin bg-linkedin"></i>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <h6>اشترك معنا</h6>
                        <hr class="deep-purple text-primary accent-2 mb-4 mt-0 d-inline-block mx-auto">
                        <div class="clearfix"></div>
                        <form action="{{ route('login_submit') }}" method="POST">
                            @csrf
                            <div class="input-group w-100">
                                <input type="email" name="email" class="form-control br-tl-3 br-bl-3" placeholder="البريد الإلكتروني" required>
                                <button type="submit" class="btn btn-primary br-tr-3 br-br-3">اشترك</button>
                            </div>
                        </form>
                        <h6 class="mb-0 mt-5">وسائل الدفع</h6>
                        <hr class="deep-purple text-primary accent-2 mb-2 mt-3 d-inline-block mx-auto">
                        <div class="clearfix"></div>
                        <ul class="footer-payments">
                            <li class="ps-0"><a href="#"><i class="fa fa-cc-amex text-muted" aria-hidden="true"></i></a></li>
                            <li><a href="#"><i class="fa fa-cc-visa text-muted" aria-hidden="true"></i></a></li>
                            <li><a href="#"><i class="fa fa-credit-card-alt text-muted" aria-hidden="true"></i></a></li>
                            <li><a href="#"><i class="fa fa-cc-mastercard text-muted" aria-hidden="true"></i></a></li>
                            <li><a href="#"><i class="fa fa-cc-paypal text-muted" aria-hidden="true"></i></a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-dark text-white p-0">
            <div class="container">
                <div class="row d-flex">
                    <div class="col-lg-12 col-sm-12 mt-3 mb-3 text-center">
                        جميع الحقوق محفوظة © {{ date('Y') }} <a href="{{ url('/') }}" class="fs-14 text-primary">عقارات الشمال</a>.
                    </div>
                </div>
            </div>
        </div>
    </footer>
</section>




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
                success:function(data)
                {
                    $('#loader').hide();
                    if(data.code == 0)
                    {
                        $.each(data.error_message, function(prefix, val) {
                            $(form).find('span.'+prefix+'_error').text(val[0]);
                        });
                    }
                    else if(data.code == 1)
                    {
                        $(form)[0].reset();
                        iziToast.success({
                            message: data.success_message,
                            position: 'topRight',
                            timeout: 5000,
                            progressBarColor: '#00FF00',
                        });
                    }

                }
            });
        });
    })(jQuery);
</script>
<div id="loader"></div>
