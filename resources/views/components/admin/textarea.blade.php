<div class="mb-3 form-group {{ $col ?? 'col-md-12' }}">
    @php $id = $id ?? $name; @endphp
    <label for="{{$id ?? ''}}" class="form-label">{{__('trans.'.($label ?? ''))}}</label>
    <textarea
        id="summernote-{{$id ?? 'textarea'}}"
        name="{{$name ?? ''}}"
        class="form-control summernote {{ $class ?? '' }}"
        @if ($required)
            required
        @endif
        @if (($required_message ?? false) && $required)
            data-validation-required-message="{{ $required_message }}"
        @elseif ($required)
            data-validation-required-message="{{__('trans.this_field_is_required')}}"
        @endif
        @if ($minLength ?? false)
            minlength="{{$minLength}}"
        data-validation-minLength-message="{{ __('trans.min_length', ['number' => $minLength])  }}"
        @endif
        @if ($maxLength ?? false)
            maxlength="{{$maxLength}}"
        data-validation-maxLength-message="{{ __('trans.max_length', ['number' => $maxLength])  }}"
        @endif
        @if ($rows ?? false)
            rows="{{$rows}}"
        @endif
        @if ($disabled ?? false)
            disabled
        @endif
        placeholder="{{__('trans.enter')}} {{__('trans.'.($placeholder ?? ''))}}"
        style="height: {{$height}}px;"
        data-height="{{$height}}"
        @if ($toolbar ?? false)
            data-toolbar="{{$toolbar}}"
        @endif
        @if ($fontNames ?? false)
            data-font-names="{{$fontNames}}"
        @endif
        @if ($fontSizes ?? false)
            data-font-sizes="{{$fontSizes}}"
        @endif
    >@if ($value ?? false)
            {{$value}}
        @endif</textarea>
</div>


@push('js_files')

    <script>
        // Function to initialize Summernote
        function initSummernote() {
            // Check if Summernote is available
            if (typeof $.fn.summernote !== 'undefined') {
                // Get current locale for language support
                const currentLocale = '{{app()->getLocale()}}';
                const language = currentLocale === 'ar' ? 'ar-AR' : 'en-US';

                // Initialize Summernote on the textarea
                $('#summernote-{{$id ?? 'textarea'}}').summernote({
                    height: {{$height ?? 300}},
                    lang: language,
                    toolbar: @if ($toolbar ?? false) {!! $toolbar !!} @else [
                        ['style', ['style']],
                            ['font', ['bold', 'underline', 'italic', 'clear']],
                            ['fontname', ['fontname']],
                            ['fontsize', ['fontsize']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['height', ['height']],
                            ['table', ['table']],
                            ['insert', ['link', 'picture', 'video']],
                            ['view', ['fullscreen', 'codeview', 'help']]
                        ] @endif,
                    fontNames: @if ($fontNames ?? false) {!! $fontNames !!} @else ['Arial', 'Arial Black', 'Comic Sans MS', 'Courier New', 'Helvetica', 'Impact', 'Tahoma', 'Times New Roman', 'Verdana'] @endif,
                    fontSizes: @if ($fontSizes ?? false) {!! $fontSizes !!} @else ['8', '9', '10', '11', '12', '14', '16', '18', '24', '36'] @endif,
                    // RTL support for Arabic
                    direction: currentLocale === 'ar' ? 'rtl' : 'ltr',
                    // Custom styling for better dashboard integration
                    popover: {
                        image: [
                            ['image', ['resizeFull', 'resizeHalf', 'resizeQuarter', 'resizeNone']],
                            ['float', ['floatLeft', 'floatRight', 'floatNone']],
                            ['remove', ['removeMedia']]
                        ],
                        link: [
                            ['link', ['linkDialogShow', 'unlink']]
                        ],
                        table: [
                            ['add', ['addRowDown', 'addRowUp', 'addColLeft', 'addColRight']],
                            ['delete', ['deleteRow', 'deleteCol', 'deleteTable']],
                        ],
                        air: [
                            ['color', ['color']],
                            ['font', ['bold', 'underline', 'clear']]
                        ]
                    }
                });
                console.log('Summernote initialized successfully for #summernote-{{$name ?? 'textarea'}} with language: ' + language);
            } else {
                console.error('Summernote is not loaded. Please make sure Summernote CSS and JS files are included.');

                setTimeout(initSummernote, 500);
            }
        }
        // Initialize when document is ready
        $(document).ready(function () {
            initSummernote();
        });

        // Also initialize when tab is shown (for tabbed interfaces)
        $(document).on('shown.bs.tab', function () {
            setTimeout(initSummernote, 100);
        });

        // Initialize when the specific tab is shown
        $(document).on('shown.bs.tab', 'button[data-bs-target="#terms"]', function () {
            setTimeout(initSummernote, 200);
        });

        // Also try to initialize after a longer delay to ensure everything is loaded
        setTimeout(initSummernote, 1000);
    </script>
@endpush
