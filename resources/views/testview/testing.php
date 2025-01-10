
<script>
    // Handle form submission via AJAX
    document.addEventListener('DOMContentLoaded', function () {
        const uploadForm = document.getElementById('uploadForm');
        const attachmentList = document.getElementById('attachmentList');

        uploadForm.addEventListener('submit', function (event) {
            event.preventDefault(); // Prevent the form from submitting normally

            // Create FormData object to send files
            let formData = new FormData(uploadForm);

            // Send AJAX request
            fetch(uploadForm.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI with newly uploaded file
                    const fileName = data.fileName; // Assuming the server returns the uploaded file name

                    // Append new attachment to the list
                    const li = document.createElement('li');
                    li.innerHTML = `
                        <i class="bi bi-paperclip me-1"></i>
                        <a href="${data.filePath}" target="_blank">${fileName}</a>
                    `;
                    attachmentList.appendChild(li);

                    // Clear the file input field
                    uploadForm.reset();
                } else {
                    // Handle error case
                    console.error('File upload failed:', data.error);
                    alert('File upload failed: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while uploading the file.');
            });
        });
    });
</script>

<script>
    $(document).ready(function () {
        // Intercept form submission
        $('form').submit(function (event) {
            event.preventDefault(); // Prevent the form from submitting normally

            // Serialize form data
            var formData = new FormData($(this)[0]);

            // Perform AJAX request
            $.ajax({
                type: $(this).attr('method'),
                url: $(this).attr('action'),
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function (response) {
                    // Display Toastr notification for success
                    toastr.success(response.success);
                    // Optionally, reload or update page content
                    // Example: window.location.reload();
                },
                error: function (xhr, status, error) {
                    // Display Toastr notification for error
                    toastr.error(xhr.responseJSON.error);
                }
            });
        });
    });
</script>

<script>
    const fetchViolationUrl = @json(route('fetchingtasfile', ['id' => 'ID_PLACEHOLDER']));

    function initializeModalScripts(modalId) {
        $('#modal-body-' + modalId + ' .remarksForm').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const saveRemarksBtn = form.find('#saveRemarksBtn');
            const spinner = saveRemarksBtn.find('.spinner-border');

            // Show spinner and disable button
            spinner.removeClass('d-none');
            saveRemarksBtn.prop('disabled', true);

            // Perform AJAX request
            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                  dataType: 'json',
                success: function (response) {
                    // Hide spinner and enable button
                    spinner.addClass('d-none');
                    saveRemarksBtn.prop('disabled', false);

                    // Show success message
                    toastr.success(response.message);

                    // Reload the modal body content
                    var fetchUrl = fetchViolationUrl.replace('ID_PLACEHOLDER', modalId);
                    fetch(fetchUrl)
                        .then(response => {
                            if (!response.ok) {
                                throw new Error('Network response was not ok');
                            }
                            return response.text();
                        })
                        .then(html => {
                            $('#modal-body-' + modalId).html(html);
                            initializeModalScripts(modalId);
                        })
                        .catch(err => {
                            console.error('Failed to reload modal content', err);
                            $('#modal-body-' + modalId).html('<p>Error loading content</p>');
                        });
                },
                error: function () {
                    // Hide spinner and enable button
                    spinner.addClass('d-none');
                    saveRemarksBtn.prop('disabled', false);

                    // Show error message
                    showAlert('Failed to save remarks. Please try again later.', 'danger');
                }
            });
        });

        $('#finishCaseFormTemplate').on('submit', function (e) {
      e.preventDefault();
      const form = $(this);
      const submitBtn = form.find('button[type="submit"]');
      const spinner = submitBtn.find('.spinner-border');

      // Show spinner and disable button
      spinner.removeClass('d-none');
      submitBtn.prop('disabled', true);

      // Perform AJAX request
      $.ajax({
          type: form.attr('method'),
          url: form.attr('action'),
          data: form.serialize(),
          dataType: 'json',
          success: function (response) {
              // Hide spinner and enable button
              spinner.addClass('d-none');
              submitBtn.prop('disabled', false);

              // Show success message
              toastr.success(response.message);

              // Close the modal
            //   let modalId = $tasFile->id? '#finishModal' + $tasFile->id : '';

            //     if (modalId!== '') {
            //         $(modalId).modal('hide');
            //     }
            $('#finishModal{{ $tasFile->id }}').modal('hide');
          },
          error: function () {
              // Hide spinner and enable button
              spinner.addClass('d-none');
              submitBtn.prop('disabled', false);

              // Show error message
              toastr.error('Failed to finish case. Please try again later.', 'danger');
          }
      });
  });

    }

    function showAlert(message, type = 'success') {
        const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
        </div>`;
        const alertElement = $(alertHtml).appendTo('body').hide().fadeIn();


    }

    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('show.bs.modal', function (event) {
            var modalId = modal.getAttribute('id').replace('exampleModal', '');
            var modalBody = modal.querySelector('.modal-body');

            var fetchUrl = fetchViolationUrl.replace('ID_PLACEHOLDER', modalId);
            console.log('Fetching URL: ', fetchUrl);

            setTimeout(() => {
                fetch(fetchUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.text();
                    })
                    .then(html => {
                        modalBody.innerHTML = html;
                        initializeModalScripts(modalId);

                        // Attach the Finish Case modal dynamically
                        const finishModalHtml = $('#finishModalTemplate').html();
                        $('#modal-body-' + modalId).append(finishModalHtml);
                        $('#finishCaseFormTemplate').attr('action', '{{ route('finish.case', ['id' => 'modalId']) }}');
                    });
            }, 1500); // 1.5 seconds delay
        });
    });


</script>
