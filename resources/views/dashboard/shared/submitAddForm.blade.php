<script>
    $(document).ready(function () {
        $(document).on('submit', '.validated-form', function (e) {
            e.preventDefault();
            var url = $(this).attr('action')
            var form = $(this)
            var submitButton = $(this).find('.submit-button')
            var submitButtonHtml = submitButton.html()
            var spiner = '<i class="ti ti-rotate-dot spinner"></i>'
            $.ajax({
                url: url,
                method: 'post',
                data: new FormData($(this)[0]),
                dataType: 'json',
                processData: false,
                contentType: false,
                beforeSend: function () {
                    submitButton.html(spiner).attr('disabled', true);
                    removeError()
                },
                success: function (response) {

                    Swal.fire({
                        icon: 'success',
                        position: 'top-center',
                        text: '{{ __('trans.added_successfully') }}',
                        showConfirmButton: false,
                        timer: 1000
                    }).then((result) => {
                          window.location.replace(response.url)
                    });

                },
                error: function (xhr) {
                    $(".submit-button").html("{{ __('trans.add') }}").attr(
                        'disabled', false)

                    if(xhr.status == 401){
                        Swal.fire({
                            icon: 'warning',
                            title: '{{ __("site.unauthenticated") }}',
                            text: '{{ __("site.please_login_first") }}',
                            showCancelButton: true,
                            confirmButtonText: '{{ __("site.go_to_login") }}',
                            cancelButtonText: '{{ __("site.cancel") }}',
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = '{{ route("login") }}';
                            }
                            // If cancelled or denied, do nothing (just close the alert)
                        });
                        return true;
                    }

                    if(xhr.status == 400){
                        Swal.fire({
                            icon: 'error',
                            position: 'top-center',
                            text: xhr.responseJSON.message,
                            showConfirmButton: false,
                            timer: 1000
                        })
                        return true
                    }

                    $(".text-danger").remove()
                    $('.store input').removeClass('border-danger')
                    let message = '';

                    $.each(xhr.responseJSON.errors, function (key, value) {
                        message += (   value + '' + '<br>' );
                        if (key == 'project_files' || key == 'image' || key == 'files' || key == 'images') {
                            Swal.fire({
                                icon: 'error',
                                position: 'top-center',
                                text: value,
                                showConfirmButton: false,
                                timer: 2000
                            });
                        } else {
                            addError(form, key, value)
                        }
                    });
                    Swal.fire({
                        position: 'center',
                        type: 'error',
                        html: message,
                        showConfirmButton: false,
                        timer: 5000,
                        confirmButtonClass: 'btn btn-primary',
                        buttonsStyling: false,
                    })

                },
                complete: function () {
                    // submitButton.html(submitButtonHtml).attr('disabled', false)
                },
            });

        });
    });


    function addError(form, key, value) {
        let newKey = key;
        // Check if the key is an array key (e.g., relatives.0.name)
        if (newKey.includes(".")) {
            const split = key.split('.');
            newKey = split[0]; // Get the base key (e.g., 'relatives')

            for (let i = 1; i < split.length; i++) {
                // Check if the current part is a number (for array index)
                if (!isNaN(split[i])) {
                    newKey += `[${split[i]}]`;
                } else {
                    newKey += `[${split[i]}]`;
                }
            }
        }

        // Find the input element related to this key
        const inputElement = $(form).find(`[name="${newKey}"]`);
        if (inputElement.length) {
            inputElement.attr('aria-invalid', 'true').closest('.form-group').addClass('issue');

            // Check if the error message container exists, if not, create it
            let helpBlock = inputElement.next('.help-block');
            if (helpBlock.length === 0) {
                helpBlock = $('<div class="help-block"></div>').insertAfter(inputElement);
            }

            // Check if the error message is already present, if not, add it
            if (!helpBlock.find(`li:contains('${value}')`).length) {
                helpBlock.append(`
                <ul role="alert">
                    <li class="text-danger">${value}</li>
                </ul>
            `);
            }
        } else {
            console.warn(`Input not found for key: ${newKey}`);
        }


    }

    function removeError() {
        $('.form-group.issue').removeClass('issue')
        $('.help-block').remove()
    }
</script>
