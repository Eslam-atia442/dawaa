<script>
    console.log('deleteAll.blade.php script loaded');
    $('.delete_all_button').addClass('d-none');

    $(document).on('change', '#checkedAll', function () {
        console.log('checkedAll changed:', this.checked);
        if (this.checked) {
            $(".checkSingle").each(function (index, element) {
                this.checked = true;
            })
            $('.delete_all_button').removeClass('d-none')
            console.log('Delete button should be visible now');
        } else {
            $(".checkSingle").each(function () {
                this.checked = false;
            })
            $('.delete_all_button').addClass('d-none');
            console.log('Delete button should be hidden now');
        }
    });
    $(document).on('click', '.checkSingle', function () {
        console.log('checkSingle clicked:', $(this).attr('id'), $(this).is(":checked"));
        if ($(this).is(":checked")) {
            var isAllChecked = 0;
            $(".checkSingle").each(function () {
                if (!this.checked)
                    isAllChecked = 1;
            })
            if (isAllChecked == 0) {
                $("#checkedAll").prop("checked", true);
            }
            $('.delete_all_button').removeClass('d-none')
            console.log('Delete button should be visible now (single check)');
        } else {
            var count = 0;
            $(".checkSingle").each(function () {
                if (this.checked)
                    count++;
            })
            if (count > 0) {
                $('.delete_all_button').removeClass('d-none')
                console.log('Delete button should be visible now (still some checked)');
            } else {
                $('.delete_all_button').addClass('d-none');
                console.log('Delete button should be hidden now (none checked)');
            }
            $("#checkedAll").prop("checked", false);
        }
    });

    $('.delete_all_button').on('click', function (e) {
        console.log('Delete button clicked');
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        e.preventDefault()
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
                var usersIds = [];
                $('.checkSingle:checked').each(function () {
                    var id = $(this).attr('id');
                    usersIds.push(id);
                });
                let count = usersIds.length
                usersIds = JSON.stringify(usersIds);
                let url = $(this).data('route')
                console.log('Selected IDs:', usersIds);
                console.log('URL:', url);
                if (count > 0) {
                    e.preventDefault();
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': csrfToken
                        },
                        type: "POST",
                        url: url,
                        data: {
                            data: usersIds
                        },

                        success: function (response) {
                            console.log('Success response:', response);
                            // click on button where class reloadTable
                            $('.reloadTable').click();
                            $('.delete_all_button').addClass('d-none');
                            Swal.fire({
                                icon: 'success',
                                position: 'top-center',
                                text: '{{__('trans.the_selected_has_been_successfully_deleted')}}',
                                showConfirmButton: false,
                                timer: 2000
                            })
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
                } else {
                    Swal.fire({
                        icon: 'error',
                        position: 'top-center',
                        text: '{{__('trans.nothing_to_delete')}}',
                        showConfirmButton: false,
                        timer: 2000
                    })
                }
            }
        })
    });
</script>
