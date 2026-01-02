<button type="button"
        class="{{ $buttonClass }}"
        id="{{ $buttonId }}"
        data-route="{{ $route }}"
        data-method="{{ $method }}"
        @if(!empty($data)) data-extra='@json($data)' @endif
        onclick="handleExportClick(this)">
    <i class="{{ $iconClass }}"></i>
    {{ $label }}
</button>

@once
@push('js_files')
<script>
    function handleExportClick(button) {
        const $btn = $(button);
        const originalHtml = $btn.html();
        const route = $btn.data('route');
        const method = $btn.data('method') || 'POST';
        const extraData = $btn.data('extra') || {};

        // Disable button and show loading
        $btn.prop('disabled', true).html('<i class="ti ti-loader ti-spin me-1"></i>{{ __('trans.exporting') }}...');

        // Prepare data
        let requestData = {
            _token: "{{ csrf_token() }}",
            ...extraData
        };

        // If we have searchArray function available, include those filters
        if (typeof searchArray === 'function') {
            requestData = { ...requestData, ...searchArray() };
        }

        $.ajax({
            url: route,
            method: method,
            data: requestData,
            success: function(response) {
                if (response.success) {
                    // Hide the button after successful export
                    $btn.hide();
                    
                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('trans.done') }}',
                        text: response.message || '{{ __('trans.export_queued') }}',
                        showConfirmButton: true
                    });
                } else {
                    $btn.prop('disabled', false).html(originalHtml);
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('trans.failed') }}',
                        text: response.error || '{{ __('trans.failed') }}',
                        showConfirmButton: true
                    });
                }
            },
            error: function(xhr) {
                $btn.prop('disabled', false).html(originalHtml);

                let errorMessage = '{{ __('trans.failed') }}';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }

                Swal.fire({
                    icon: 'error',
                    title: '{{ __('trans.failed') }}',
                    text: errorMessage,
                    showConfirmButton: true
                });
            }
        });
    }
</script>
@endpush
@endonce

