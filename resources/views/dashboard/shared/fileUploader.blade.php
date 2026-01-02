
<script>
$(document).ready(function () {
    function previewFile(input, previewContainer) {
        var files = input.files;

        // Clear the preview container if input is not multiple
        if (!input.multiple) {
            $(previewContainer).empty();
        }

        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            var reader = new FileReader();
            reader.onload = function (e) {
                var result = e.target.result;
                var fileType = file.type.split('/')[0];
                var previewElement;

                // Create a container for the file preview
                var fileContainer = $('<div class="file-container"></div>');
                var deleteButton = $('<button class="delete-btn btn-danger"><i class="fa-regular fa-trash-can"></i></button>');
                var downloadButton = $('<a class="download-btn btn-light" download><i class="fa-solid fa-download"></i></a>').attr('href', result);

                if (fileType === 'image') {
                    previewElement = $('<img>').attr('src', result);
                } else if (fileType === 'video') {
                    previewElement = $('<video controls>').attr('src', result);
                } else if (file.type === 'application/pdf') {
                    previewElement = $('<div class="file-icon"><i class="fa-solid fa-file-pdf text-danger"></i></div>');
                } else if (file.type === 'application/msword' || file.type === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                    previewElement = $('<div class="file-icon"><i class="fa-solid fa-file-word text-info"></i></div>');
                } else if (file.type === 'application/vnd.ms-excel' || file.type === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                    previewElement = $('<div class="file-icon"><i class="fa-solid fa-file-excel text-success"></i></div>');
                } else {
                    previewElement = $('<div class="file-icon"><i class="fa-solid fa-file"></i></div>');
                }

                // Append the preview element and buttons to the file container
                fileContainer.append(previewElement);
                fileContainer.append(deleteButton);
                fileContainer.append(downloadButton);
                $(previewContainer).append(fileContainer);

                // Event listener for the delete button
                // deleteButton.on('click', function () {
                //     fileContainer.remove();
                // });
            };
            reader.readAsDataURL(file);
        }
    }

    $('.delete-btn').on('click', function () {


        let id = $(this).data('id');
        let url = window.location.origin + '/admin/v1/files/' + id;
        let csrfToken = $('meta[name="csrf-token"]').attr('content');
        if (id) {
            Swal.fire({
                title: `@lang('trans.are_you_sure')`,
                text: `@lang('trans.you_will_not_be_able_to_revert_this')`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: `@lang('trans.yes_delete_it')`,
                cancelButtonText: `@lang('trans.no_cancel_it')`,
            }).then((result) => {

                if (result.isConfirmed) {
                    $(this).closest('.file-container').remove();
                    $.ajax({
                        type: 'POST',
                        url: url,
                        data: {
                            _token: csrfToken,
                            _method: "DELETE" // Set request type to DELETE
                        },
                        success: function (response) {
                            console.log(response);
                        },
                        error: function (xhr, status, error) {
                            console.log(xhr.responseText);
                        }
                    });
                }

            });

        }
    });


    $('.upload-input').on('change', function () {
        var previewContainer = $(this).closest('.upload-box').next('.preview');
        previewFile(this, previewContainer);
    });
});
</script>
