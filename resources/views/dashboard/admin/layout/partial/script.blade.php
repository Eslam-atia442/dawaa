<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->


<script src="{{asset('/')}}assets/vendor/js/bootstrap.js"></script>
<script src="{{asset('/')}}assets/vendor/js/menu.js"></script>
<!-- Main JS -->
<script src="{{asset('/')}}assets/js/main.js"></script>
<script src="{{asset('/')}}assets/vendor/libs/jquery/jquery.js"></script>
<script src="{{asset('/')}}assets/vendor/libs/select2/select2.js"></script>
<script>
    window.select2Language = '{{ app()->getLocale() }}';
    window.select2DefaultPlaceholder = '{{ __("trans.select_value") }}';
     
    var select2Translations = {};
    
    @if(app()->getLocale() == 'ar')
    select2Translations.ar = {
        errorLoading: function () {
            return "لا يمكن تحميل النتائج";
        },
        inputTooLong: function (e) {
            var t = e.input.length - e.maximum;
            return "الرجاء حذف " + t + " عناصر";
        },
        inputTooShort: function (e) {
            var t = e.minimum - e.input.length;
            return "الرجاء إضافة " + t + " عناصر";
        },
        loadingMore: function () {
            return "جاري تحميل المزيد من النتائج…";
        },
        maximumSelected: function (e) {
            return "يمكنك اختيار " + e.maximum + " عناصر فقط";
        },
        noResults: function () {
            return "{{ __('trans.there_are_no_matches_matching') }}";
        },
        searching: function () {
            return "جاري البحث…";
        },
        removeAllItems: function () {
            return "قم بإزالة جميع العناصر";
        },
        removeItem: function () {
            return "قم بإزالة العنصر";
        },
        search: function () {
            return "{{ __('trans.search') }}";
        }
    };
    @else
    select2Translations.en = {
        errorLoading: function () {
            return "The results could not be loaded.";
        },
        inputTooLong: function (e) {
            var t = e.input.length - e.maximum;
            return "Please delete " + t + " character" + (t == 1 ? "" : "s");
        },
        inputTooShort: function (e) {
            var t = e.minimum - e.input.length;
            return "Please enter " + t + " or more characters";
        },
        loadingMore: function () {
            return "Loading more results…";
        },
        maximumSelected: function (e) {
            return "You can only select " + e.maximum + " item" + (e.maximum != 1 ? "s" : "");
        },
        noResults: function () {
            return "{{ __('trans.there_are_no_matches_matching') }}";
        },
        searching: function () {
            return "Searching…";
        },
        removeAllItems: function () {
            return "Remove all items";
        },
        removeItem: function () {
            return "Remove item";
        },
        search: function () {
            return "{{ __('trans.search') }}";
        }
    };
    @endif
    
    // Set default language for all Select2 instances
    if (typeof $.fn.select2 !== 'undefined') {
        var langKey = window.select2Language || 'en';
        $.fn.select2.defaults.set('language', select2Translations[langKey] || select2Translations.en);
    }
</script>
<script src="{{asset('/')}}assets/vendor/libs/tagify/tagify.js"></script>
<script src="{{asset('/')}}assets/vendor/libs/bootstrap-select/bootstrap-select.js"></script>

<!-- Summernote JS -->
<script src="{{asset('/')}}assets/summernote/summernote-bs5.min.js"></script>
<!-- Summernote Language Files -->
<script src="{{asset('/')}}assets/summernote/lang/summernote-en-US.min.js"></script>
<script src="{{asset('/')}}assets/summernote/lang/summernote-ar-AR.min.js"></script>

<script src="{{asset('/')}}assets/vendor/libs/popper/popper.js"></script>
<script src="{{asset('/')}}assets/vendor/libs/node-waves/node-waves.js"></script>
<script src="{{asset('/')}}assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
<script src="{{asset('/')}}assets/vendor/libs/hammer/hammer.js"></script>


<!-- endbuild -->

<!-- Vendors JS -->

<!-- Page JS -->
<script src="{{asset('/')}}assets/js/forms-selects.js"></script>
{{--<script src="{{ asset('assets/validation/jqBootstrapValidation.js') }}"></script>--}}

<script>
    $(document).on('click', '.show_filter', function () {
        $('.filter_div').fadeToggle(200);
    });
</script>
<script>
    $(document).ready(function () {
        $('.dropdown-item.selected').on('click', function () {
            const selectedLanguage = $(this).data('language');
            const id = $('#language-dropdown').data('id');
            let url = `${window.location}`;
            $.ajax({
                url: url,
                type: 'GET',
                headers: {
                    'Accept-Language': selectedLanguage
                },
                success: function (data) {
                    location.reload();
                },
                error: function (error) {
                    console.error('Error:', error);
                }
            });
        });
    });

</script>
<script>
    function toggleField(url) {
        $.ajax({
            url: url,
            type: 'post',
            data: {
                _token: "{{ csrf_token() }}",
            },
            success: function (data) {
                $('.reloadTable').click();

                Swal.fire({
                    icon: 'success',
                    position: 'top-center',
                    text: '{{__('trans.done')}}',
                    showConfirmButton: false,
                    timer: 1000
                })
            },
            error: function (error) {
                Swal.fire({
                    icon: 'error',
                    position: 'top-center',
                    text: '{{__('trans.failed')}}',
                    showConfirmButton: false,
                    timer: 1000
                })
            }
        });

    }
</script>


<script src="{{asset('/')}}assets/vendor/libs/sweetalert2/sweetalert2.js"></script>
<script src="{{asset('/')}}assets/js/extended-ui-sweetalert2.js"></script>
@include('dashboard.shared.fileUploader')
@vite(['resources/js/app.js'])
@stack('js_files')


