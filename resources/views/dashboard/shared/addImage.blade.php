<script>
    // let accountUserImage = document.getElementById('uploadedAvatar');
    // const fileInput = document.querySelector('.account-file-input'),
    //   resetFileInput = document.querySelector('.account-image-reset');

    // if (accountUserImage) {
    //   const resetImage = accountUserImage.src;
    //   fileInput.onchange = () => {
    //     if (fileInput.files[0]) {
    //       accountUserImage.src = window.URL.createObjectURL(fileInput.files[0]);
    //     }
    //   };
    //   resetFileInput.onclick = () => {
    //     fileInput.value = '';
    //     accountUserImage.src = resetImage;
    //   };
    // }
    $(document).ready(function() {
        const $fileInput        = $('.account-file-input')
        let   $accountUserImage = $fileInput.parents('.image_groub').find('.uploadedAvatar');
        const $resetFileInput   = $('.account-image-reset');

        // if ($accountUserImage.length) {
            const resetImage = $accountUserImage.attr('src');
            $(document).on('change', '.account-file-input', function(e) {
              if (this.files && this.files[0]) {
                console.log(this);
                  $(this).parent().parent().parent().find('.uploadedAvatar').attr('src', window.URL.createObjectURL(this.files[0]));
                }
            })
            // $('.account-file-input').on('change', function() {
            //     if (this.files && this.files[0]) {
            //       $(this).parents('.image_groub').find('.uploadedAvatar').attr('src', window.URL.createObjectURL(this.files[0]));
            //     }
            // });

            $resetFileInput.on('click', function() {
                $fileInput.val('');
                $accountUserImage.attr('src', resetImage);
            });
        // }
    });
</script>