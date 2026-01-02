<script>
    $(document).on('click', '.delete-row', function(e) {
        e.preventDefault();

        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        var url = $(this).data('url');

        Swal.fire({
            title: `@lang('trans.are_you_sure')`,
            text: `@lang('trans.you_will_not_be_able_to_revert_this')`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: `@lang('trans.yes_delete_it')`,
            cancelButtonText:  `@lang('trans.no_cancel_it')`,
        }).then((result) => {

            if (result.value) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': csrfToken
                    },
                    data: {
                        '_method': 'DELETE'
                    },
                    type: "POST",  // Use POST method because we're overriding it to DELETE
                    url: url,
                    success: (response) => {
                        $('.delete_all_button').addClass('d-none');
                        Swal.fire({
                            icon: 'success',
                            position: 'top-center',
                            text: '{{ __('trans.the_selected_has_been_successfully_deleted') }}',
                            showConfirmButton: false,
                            timer: 2000
                        });
                        setTimeout(() => {
                            if (response.reload) window.location.reload();
                        }, 1000);
                        getData({
                            'searchArray': searchArray()
                        });
                    },
                    error: (jqXHR, textStatus, errorThrown) => {
                        Swal.fire({
                            icon: 'error',
                            position: 'top-center',
                            text: jqXHR.responseJSON.error ?? `{{ __('trans.error_occurred_during_operation') }}`,
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
            }
        });
    });

</script>
