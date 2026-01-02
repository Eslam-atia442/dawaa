<script>
     $(document).ready(function () {
        $(document).on('click', '.{{ $button }}', function (e) {
            e.preventDefault();
            form = $('#{{$form}}')[0];
            var url = ($('#{{$form}}').attr('action'));
            let data = new FormData($('#{{$form}}')[0]);
            $('.{{ $button }}').attr('disabled', true);

            $.ajax({
                url: url,
                method: 'post',
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,
                beforeSend: function () {

                    $('.{{ $button }}').attr('disabled', true);
                    removeError()
                },
                success: function (response) {
                    Swal.fire({
                        icon: 'success',
                        position: 'top-center',
                        text: response.message ? response.message : '{{ __('site.added_successfully') }}',
                        showConfirmButton: false,
                        timer: 1000
                    }).then((result) => {
                        if(response.url){
                            console.log(url)
                            window.location.replace(response.url)
                            return true
                        }
                        // reload page
                        window.location.reload();
                    });

                },
                error: function (xhr) {
                    $('.{{ $button }}').attr('disabled', false);

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
                        timer: 1500,
                        confirmButtonClass: 'btn btn-primary',
                        buttonsStyling: false,
                    })

                },
                complete: function () {
                    $('.{{ $button }}').attr('disabled', false);
                },
            });

        });
    });
    function addError(form, key, value) {
        let newKey = key;
        if (newKey.includes(".")) {
            const split = key.split('.');
            newKey = split[0];

            for (let i = 1; i < split.length; i++) {
                if (!isNaN(split[i])) {
                    newKey += `[${split[i]}]`;
                } else {
                    newKey += `[${split[i]}]`;
                }
            }
        }

        const inputElement = $(form).find(`[name="${newKey}"]`);
        if (inputElement.length) {
            inputElement.attr('aria-invalid', 'true').closest('.form-group').addClass('issue');
            let helpBlock = inputElement.next('.help-block');
            if (helpBlock.length === 0) {
                helpBlock = $('<div class="help-block"></div>').insertAfter(inputElement);
            }
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
