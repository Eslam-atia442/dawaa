<script>
    $(document).ready(function() {
      $(document).on('submit', '.validated-form', function(e) {
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
            submitButton.html(spiner).attr('disabled',true);
          },
          success: function(response) {
            Swal.fire({
              icon: 'success',
              position: 'top-start',
              text: '{{ __('trans.added_successfully') }}',
              showConfirmButton: false,
              timer: 2000
            }).then((result) => {
              window.location.replace(response.url)
            });

          },
          error: function(xhr) {
            $(".submit_button").html("{{ __('trans.add') }}").attr(
              'disabled', false)
            $(".text-danger").remove()
            $('.store input').removeClass('border-danger')

            $.each(xhr.responseJSON.errors, function(key, value) {
              $.each(xhr.responseJSON.errors, function(key,value) {
                addError(form , key , value)
              });
            });
          },complete: function () {
            submitButton.html(submitButtonHtml).attr('disabled',false)
          },
        });

      });
    });
    function addError(form , key , value){
      $(form).find('[name='+key+']').attr('aria-invalid','true').closest('.form-group').addClass('issue')
      $(form).find('[name='+key+']').next('.help-block').append(`
        <ul role="alert">
          <li class="text-danger">${value}</li>
        </ul>
      `);
    }
  </script>
