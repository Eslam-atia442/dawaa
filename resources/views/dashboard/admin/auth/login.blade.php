<!DOCTYPE html>

<html
    lang="en"
    class="light-style customizer-hide"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="../../assets/"
    data-template="vertical-menu-template">
<head>
    <meta charset="utf-8"/>
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"/>

    <title>Login Cover - Pages | Vuexy - Bootstrap Admin Template</title>

    <meta name="description" content=""/>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{asset('/')}}assets/img/favicon/favicon.ico"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&ampdisplay=swap"
        rel="stylesheet"/>

    <!-- Icons -->
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/fonts/fontawesome.css"/>
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/fonts/tabler-icons.css"/>
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/fonts/flag-icons.css"/>

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/css/rtl/core.css" class="template-customizer-core-css"/>
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/css/rtl/theme-default.css"
          class="template-customizer-theme-css"/>
    <link rel="stylesheet" href="{{asset('/')}}assets/css/demo.css"/>

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/libs/node-waves/node-waves.css"/>
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css"/>
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/libs/typeahead-js/typeahead.css"/>
    <!-- Vendor -->
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/libs/@form-validation/umd/styles/index.min.css"/>

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/css/pages/page-auth.css"/>
    <link rel="stylesheet" href="{{asset('/')}}assets/vendor/libs/sweetalert2/sweetalert2.css"/>


    <!-- Helpers -->
    <script src="{{asset('/')}}assets/vendor/js/helpers.js"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    @include('dashboard.shared.template-customizer')
    <script src="{{asset('/')}}assets/js/config.js"></script>
</head>

<body>
<!-- Content -->

<div class="authentication-wrapper authentication-cover authentication-bg">
    <div class="authentication-inner row">
        <!-- /Left Text -->
        <div class="d-none d-lg-flex col-lg-7 p-0">


            <div class="auth-cover-bg auth-cover-bg-color d-flex justify-content-center align-items-center">
                <div class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0 position-absolute"
                     style="top: 2%;left: 2%;">
                    <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown"
                       aria-expanded="false">
                        <i class="ti ti-md ti-sun"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
                        <li>
                            <a class="dropdown-item" href="javascript:void(0);" data-theme="light">
                                <span class="align-middle"><i class="ti ti-sun me-2"></i>Light</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0);" data-theme="dark">
                                <span class="align-middle"><i class="ti ti-moon me-2"></i>Dark</span>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="javascript:void(0);" data-theme="system">
                                <span class="align-middle"><i class="ti ti-device-desktop me-2"></i>System</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <img
                    src="{{asset('/')}}assets/img/illustrations/auth-login-illustration-light.png"
                    alt="auth-login-cover"
                    class="img-fluid my-5 auth-illustration"
                    data-app-light-img="illustrations/auth-login-illustration-light.png"
                    data-app-dark-img="illustrations/auth-login-illustration-dark.png"/>

                <img
                    src="{{asset('/')}}assets/img/illustrations/bg-shape-image-light.png"
                    alt="auth-login-cover"
                    class="platform-bg"
                    data-app-light-img="illustrations/bg-shape-image-light.png"
                    data-app-dark-img="illustrations/bg-shape-image-dark.png"/>
            </div>
        </div>
        <!-- /Left Text -->

        <!-- Login -->
        <div class="d-flex col-12 col-lg-5 align-items-center p-sm-5 p-4">
            <div class="w-px-400 mx-auto">
                <!-- Logo -->
                <div class="app-brand mb-4">
                    <a href="index.html" class="app-brand-link gap-2">
                <span class="app-brand-logo demo">
                  <svg width="32" height="22" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z"
                        fill="#7367F0"/>
                    <path
                        opacity="0.06"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z"
                        fill="#161616"/>
                    <path
                        opacity="0.06"
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z"
                        fill="#161616"/>
                    <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z"
                        fill="#7367F0"/>
                  </svg>
                </span>
                    </a>
                </div>
                <!-- /Logo -->
                <h3 class="mb-1 fw-bold">Welcome to {{ env('APP_NAME') }} ! 👋</h3>

                <form class="mb-3 form" action="{{route('login')}}" method="POST" novalidate>
                 

                    <div class="mb-3 form-group">
                        <label for="email" class="form-label">{{__('trans.email')}}</label>
                        <input type="text" class="form-control"
                               required data-validation-required-message="{{ __('trans.this_field_is_required') }}"
                               required data-validation-email-message="{{ __('trans.email_formula_is_incorrect') }}"
                               name="email" placeholder="{{__('trans.enter')}} {{__('trans.email')}}"/>
                    </div>


                    <div class="mb-3 form-password-toggle">

                        <div class=" form-group">
                            <label for="email" class="form-label">{{__('trans.password')}}</label>

                            <input type="password" id="password" minlength="6"
                                   data-validation-required-message="{{ __('trans.this_field_is_required') }}"
                                   data-validation-minlength-message="{{ __('trans.password_min') }}"
                                   class="form-control" name="password"
                                   placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                   aria-describedby="password"/>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" name="remember" type="checkbox" id="remember-me"/>
                            <label class="form-check-label" for="remember-me"> {{__('trans.remember_me')}} </label>
                        </div>
                    </div>

                    <button type="submit"
                            class="btn btn-primary d-grid w-100 submitButton">{{__('trans.login')}}</button>

                </form>


            </div>
        </div>
        <!-- /Login -->
    </div>
</div>

<!-- / Content -->

<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->

<script src="{{asset('/')}}assets/vendor/libs/jquery/jquery.js"></script>
<script src="{{asset('/')}}assets/vendor/libs/popper/popper.js"></script>
<script src="{{asset('/')}}assets/vendor/js/bootstrap.js"></script>
<script src="{{asset('/')}}assets/vendor/libs/node-waves/node-waves.js"></script>
<script src="{{asset('/')}}assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="{{asset('/')}}assets/vendor/libs/hammer/hammer.js"></script>
<script src="{{asset('/')}}assets/vendor/libs/i18n/i18n.js"></script>
<script src="{{asset('/')}}assets/vendor/libs/typeahead-js/typeahead.js"></script>
<script src="{{asset('/')}}assets/vendor/js/menu.js"></script>

<!-- endbuild -->

<!-- Vendors JS -->
<script src="{{asset('/')}}assets/vendor/libs/@form-validation/umd/bundle/popular.min.js"></script>
<script src="{{asset('/')}}assets/vendor/libs/@form-validation/umd/plugin-bootstrap5/index.min.js"></script>
<script src="{{asset('/')}}assets/vendor/libs/@form-validation/umd/plugin-auto-focus/index.min.js"></script>

<!-- Main JS -->
<script src="{{asset('/')}}assets/js/main.js"></script>

<!-- Page JS -->
<script src="{{asset('/')}}assets/js/pages-auth.js"></script>
<script src="{{asset('/')}}assets/vendor/libs/sweetalert2/sweetalert2.js"></script>
<script src="{{asset('/')}}assets/js/extended-ui-sweetalert2.js"></script>

<script>
    $(document).on('submit', '.form', function (e) {
        e.preventDefault();
        var url = $(this).attr('action')
        var form = $(this)
        var submitButton = $(this).find('.submitButton')
        var submitButtonHtml = submitButton.html()
        var spiner = '<i class="ti ti-rotate-dot spinner"></i>'
        let data = new FormData($(this)[0])
        data.append('_token', getNewToken())
        function getNewToken() {
            return "{{ csrf_token() }}"
        }
        $.ajax({
            url: url,
            method: 'post',
            data: data,
            dataType: 'json',
            processData: false,
            contentType: false,
            beforeSend: function () {
                submitButton.html(spiner).attr('disabled', true);
            },
            success: (response) => {
                Swal.fire({
                    icon: 'success',
                    position: 'top-center',
                    text: `{{__('trans.login_success') }}`,
                    showConfirmButton: false,
                    timer: 500
                }).then((result) => {
                    window.location.replace(response.url)
                });
            },
            error: (xhr) => {
                submitButton.html(spiner).attr('disabled', false);

                $(".submit-button").html("{{ __('trans.add') }}").attr(
                    'disabled', false)
                $(".text-danger").remove()
                $('.store input').removeClass('border-danger')

                $.each(xhr.responseJSON.errors, function (key, value) {
                    if (key == 'image') {
                        Swal.fire({
                            icon: 'error',
                            position: 'top-start',
                            text: value,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    } else {
                        addError(form, key, value)
                    }
                });
            }, complete: function () {
                submitButton.html(submitButtonHtml).attr('disabled', false)
            }
        });


    });

    function addError(form, key, value) {
        let newKey = key;
        if (newKey.indexOf(".") >= 0) {
            var split = key.split('.');
            newKey = split[0];
            for (let i = 1; i < split.length; i++) {
                newKey += '\\[' + split[i] + '\\]';
            }
        }
        $(form).find('[name=' + newKey + ']').attr('aria-invalid', 'true').closest('.form-group').addClass('issue')
        let helpBlock = $(form).find('[name=' + newKey + ']').next('.help-block');
        if (helpBlock.length === 0) {
            helpBlock = $('<div class="help-block"></div>').insertAfter($(form).find('[name=' + newKey + ']'));
        }
        helpBlock.append(`
            <ul role="alert">
              <li class="text-danger">${value}</li>
            </ul>
        `);


    }

    function removeError() {
        $('.form-group.issue').removeClass('issue')
        $('.help-block').remove()
    }
</script>
</body>
</html>
