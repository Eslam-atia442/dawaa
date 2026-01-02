<script>
    $(document).ready(function() {
        $(document).on('submit', '.form', function(e) {
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
                beforeSend: function() {
                    submitButton.html(spiner).attr('disabled', true);
                },
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        position: 'top-start',
                        text: '{{ __('trans.update_successfullay') }}',
                        showConfirmButton: false,
                        timer: 2000
                    })
                    // .then((result) => {
                    //     window.location.replace(response.url)
                    // });

                },
                error: function(xhr) {
                    $(".submit-button").html(submitButtonHtml).attr(
                        'disabled', false)
                    $(".text-danger").remove()
                    $('.store input').removeClass('border-danger')

                    Swal.fire({
                        icon: 'error',
                        position: 'top-start',
                        text: '{{ __('trans.failed') }}',
                        showConfirmButton: false,
                        timer: 2000
                    })
                    // .then((result) => {
                    //     window.location.replace(response.url)
                    // });

                    $.each(xhr.responseJSON.errors, function(key, value) {
                        addError(form, key, value)
                    });

                },
                complete: function() {
                    submitButton.html(submitButtonHtml).attr('disabled', false)
                },
            });

        });
    });

    function addError(form, key, value) {
        let newKey = key;
        if (newKey.indexOf(".") >= 0) {
            var split = key.split('.')
            newKey = split[0] + '\\[' + split[1] + '\\]';
        }
        console.log(newKey);
        $(form).find('[name=' + newKey + ']').attr('aria-invalid', 'true').closest('.form-group').addClass('issue')
        $(form).find('[name=' + newKey + ']').next('.help-block').append(`
            <ul role="alert">
              <li class="text-danger">${value}</li>
            </ul>
          `);
    }
</script>
