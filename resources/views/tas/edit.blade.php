
@section('title', env('APP_NAME'))

@include('layouts.title')

<body>
    <style>
        /* Hide the spinner arrows for number input */
        input[type="number"] {
            -moz-appearance: textfield; /* Firefox */
        }
    
        input[type="number"]::-webkit-outer-spin-button,
        input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .capitalize {
    text-transform: uppercase;
    }
    </style>
  <!-- ======= Header ======= -->
@include('layouts.header')

  <!-- ======= Sidebar ======= -->
 @include('layouts.sidebar')

  <main id="main" class="main">
    @if (session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif


<!-- Recent Violations -->
<div class="col-12">
    <div class="card recent-violations overflow-auto">
        <div class="card-body">
            <h5 class="card-title"> Contested Cases<span></span></h5>
            <table class="table table-striped table-bordered table-hover datatable">
            <thead class="thead-light">
        <tr>
            <th scope="col">Record Status</th>
            <th scope="col">Tas No.</th>
            <th scope="col">TOP</th>
            <th scope="col">Driver</th>
            <th scope="col">Apprehending Officer</th>
            <th scope="col">Department</th>
            <th scope="col">Type of Vehicle</th>
         
            <th scope="col">Transaction No.</th>
            <th scope="col">Date Received</th>
            <th scope="col">Plate No.</th>
          
            <th scope="col">Case Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($recentViolationsToday as $violation)
        <tr class="table-row" data-bs-toggle="modal" data-bs-target="#editViolationModal{{ $violation->id }}">
            <td class="align-middle symbol-cell {{ symbolBgColor($violation->symbols) }}" onclick="openModal('{{ $violation->symbols }}')">
                @if($violation->symbols === 'complete')
                    <span class="text-white"><i class="bi bi-check-circle-fill"></i> Complete</span>
                @elseif($violation->symbols === 'incomplete')
                    <span class="text-white"><i class="bi bi-exclamation-circle-fill"></i> Incomplete</span>
                @elseif($violation->symbols === 'deleting')
                    <span class="text-white"><i class="bi bi-trash-fill"></i> Deleting</span>
                @else
                    <span class="text-white"><i class="bi bi-question-circle-fill"></i> Incomplete</span>
                @endif
            </td>
            <td class="align-middle">{{ $violation->case_no }}</td>
            <td class="align-middle">{{ $violation->top }}</td>
            <td class="align-middle">{{ $violation->driver }}</td>
            <td class="align-middle">{{ $violation->apprehending_officer }}</td>
            <td class="align-middle">@foreach ($violation->relatedofficer as $offdep){{$offdep->department}}@endforeach</td>
            <td class="align-middle">{{ $violation->typeofvehicle }}</td>
            <td class="align-middle">{{ $violation->transaction_no }}</td>
            <td class="align-middle">{{ $violation->date_received }}</td>
            <td class="align-middle">{{ $violation->plate_no }}</td>
            <td class="align-middle" style="background-color: {{ getStatusColor($violation->status) }}">
                @if($violation->status === 'closed')
                    <span><i class="bi bi-check-circle-fill"></i> Closed</span>
                @elseif($violation->status === 'in-progress')
                    <span><i class="bi bi-arrow-right-circle-fill"></i> In Progress</span>
                @elseif($violation->status === 'settled')
                    <span><i class="bi bi-check-circle-fill"></i> Settled</span>
                @elseif($violation->status === 'unsettled')
                    <span><i class="bi bi-exclamation-circle-fill"></i> Unsettled</span>
                @else
                    <span><i class="bi bi-question-circle-fill"></i> Unknown</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>


        </div>
    </div>
</div><!-- End Recent Violations -->

@foreach($recentViolationsToday as $violation)
<div class="modal fade" id="editViolationModal{{ $violation->id }}" tabindex="-1" aria-labelledby="editViolationModalLabel{{ $violation->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 80%;">
        <div class="modal-content">
            
                <div class="modal-header">
                    <h5 class="modal-title" id="editViolationModalLabel{{ $violation->id }}">
                        <span><i class="bi bi-pencil-square"></i></span>
                        Edit Violation
                    </h5>
              
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            
                <div class="modal-body" id="modal-body-{{ $violation->id }}">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading...
                </div>
                
        </div>
    </div>
</div>

@endforeach



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // Event listener for delete violation button
        $(document).on('click', '.delete-cases', function() {
            var violationId = $(this).data('violation-id');
            showDeleteConfirmation(violationId);
        });

        // Function to show Toastr confirmation prompt
        function showDeleteConfirmation(violationId) {
            // Configure Toastr options
            toastr.options = {
                closeButton: false,
                progressBar: true,
                positionClass: 'toast-top-center',
                preventDuplicates: true,
                onclick: null,
                showDuration: '300',
                hideDuration: '1000',
                timeOut: '0', // To make it sticky
                extendedTimeOut: '0', // To make it sticky
                showEasing: 'swing',
                hideEasing: 'linear',
                showMethod: 'fadeIn',
                hideMethod: 'fadeOut'
            };

            var confirmationPrompt = `
                <div class="confirmation-prompt">
                    <p class="prompt-text">Are you sure you want to delete this Case?</p>
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger btn-confirm-yes">Yes</button>
                        <button type="button" class="btn btn-secondary btn-confirm-no">No</button>
                    </div>
                </div>
            `;

            toastr.info(confirmationPrompt, 'Confirm Deletion', {
                closeButton: true, // To show a close button
                closeHtml: '<button><i class="fas fa-times"></i></button>', // Custom HTML for the close button
            });

            // Unbind previously bound event handlers to prevent multiple executions
            $(document).off('click', '.btn-confirm-yes').on('click', '.btn-confirm-yes', function() {
                deleteViolation(violationId);
                toastr.clear(); // Clear the Toastr notification
            });

            $(document).off('click', '.btn-confirm-no').on('click', '.btn-confirm-no', function() {
                toastr.clear(); // Clear the Toastr notification
            });
        }

        // Function to handle deletion request
        function deleteViolation(violationId) {
            var fetchUrl = '{{ route("violations.delete", ["id" => "ID_PLACEHOLDER"]) }}'.replace('ID_PLACEHOLDER', violationId);

            $.ajax({
                type: 'DELETE',
                url: fetchUrl,
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        $('.table-row[data-bs-target="#editViolationModal' + violationId + '"]').remove();
                        localStorage.removeItem('modalId');

                        // Hide modal
                        $('#editViolationModal' + violationId).modal('hide');
                        toastr.success(response.success);
                        
                        
                    } else {
                        toastr.error(response.error);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Failed to delete violation.');
                    console.error('Error:', error);
                }
            });
        }
    });
</script>
<script>
    // Function to handle the form submission
    $('form').submit(function(event) {
        event.preventDefault(); // Prevent the default form submission
        
        var form = $(this); // Get the form element
        var url = form.attr('action'); // Get the form action URL
        var method = form.attr('method'); // Get the form submission method
        var formData = new FormData(this); // Create FormData object to send form data

        // Make AJAX request to the server
        $.ajax({
            type: method, // Set the request method
            url: url, // Set the request URL
            data: formData, // Set the form data
            processData: false, // Prevent jQuery from processing the data
            contentType: false, // Prevent jQuery from setting the content type
            success: function(response) { // Success callback function
                // Check if the response contains a success message
                if (response && response.message) {
                    // Show Toastr success notification
                    toastr.success(response.message);
                }
            },
            error: function(xhr, status, error) { // Error callback function
                // Check if the response contains an error message
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    // Show Toastr error notification
                    toastr.error(xhr.responseJSON.error);
                }
            }
        });
    });
</script>
<script>

        // Function to handle saving changes and reloading modal
    function saveChangesAndReloadModal(violationId) {
        var form = $('#editViolationForm' + violationId);
        var formData = new FormData(form[0]);

        $.ajax({
            type: 'POST',
            url: form.attr('action'),
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success(response.success); // Display success message (if needed)

                // Update modal content with the updated violation details
                reloadModalContent(violationId);
                // Close the modal (if needed)
                // $('#editViolationModal' + violationId).modal('hide');
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.error); // Display error message (if needed)
            }
        });
    }
    const fetchViolationUrl = @json(route('fetchingeditfile', ['id' => 'ID_PLACEHOLDER']));

    function initializeModalScripts(modalId) {
        // Handle remarks form submission
        $('#modal-body-' + modalId + ' .remarksForm').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const saveRemarksBtn = form.find('#saveRemarksBtn');
            const spinner = saveRemarksBtn.find('.spinner-border');

            spinner.removeClass('d-none');
            saveRemarksBtn.prop('disabled', true);

            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                success: function (response) {
                    spinner.addClass('d-none');
                    saveRemarksBtn.prop('disabled', false);
                    showAlert(response.message);

                    reloadModalContent(modalId);
                },
                error: function () {
                    spinner.addClass('d-none');
                    saveRemarksBtn.prop('disabled', false);
                    showAlert('Failed to save remarks. Please try again later.', 'danger');
                }
            });
        });

        // Handle Finish Case form submission
        $('#finishCaseFormTemplate').on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const submitBtn = form.find('button[type="submit"]');
            const spinner = submitBtn.find('.spinner-border');

            spinner.removeClass('d-none');
            submitBtn.prop('disabled', true);

            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                success: function (response) {
                    spinner.addClass('d-none');
                    submitBtn.prop('disabled', false);
                    showAlert(response.message);
                    $('#finishModalTemplate').modal('hide');
                },
                error: function () {
                    spinner.addClass('d-none');
                    submitBtn.prop('disabled', false);
                    showAlert('Failed to finish case. Please try again later.', 'danger');
                }
            });
        });

        // Handle Delete Case form submission
        $('#confirmDeleteModal' + modalId).on('submit', function (e) {
            e.preventDefault();
            const form = $(this);
            const deleteBtn = form.find('button[type="submit"]');
            const spinner = deleteBtn.find('.spinner-border');

            spinner.removeClass('d-none');
            deleteBtn.prop('disabled', true);

            $.ajax({
                type: form.attr('method'),
                url: form.attr('action'),
                data: form.serialize(),
                success: function (response) {
                    spinner.addClass('d-none');
                    deleteBtn.prop('disabled', false);
                    showAlert(response.message);
                    $('#confirmDeleteModal' + modalId).modal('hide'); // Hide delete confirmation modal
                    reloadModalContent(modalId); // Reload modal content after deletion
                },
                error: function () {
                    spinner.addClass('d-none');
                    deleteBtn.prop('disabled', false);
                    showAlert('Failed to delete case. Please try again later.', 'danger');
                }
            });
        });
    }

    // Function to reload modal content
    function reloadModalContent(modalId) {
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
                initializeModalScripts(modalId); // Reinitialize scripts after content reload
            })
            .catch(err => {
                console.error('Failed to reload modal content', err);
                $('#modal-body-' + modalId).html('<p>Error loading content</p>');
            });
    }

    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('show.bs.modal', function (event) {
            var modalId = modal.getAttribute('id').replace('editViolationModal', ''); 
            var modalBody = modal.querySelector('.modal-body');
            
            var fetchUrl = fetchViolationUrl.replace('ID_PLACEHOLDER', modalId);
            
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

    $(document).ready(function () {
        // Check if there's a cached modal ID and open it
        var cachedModalId = localStorage.getItem('modalId');
        if (cachedModalId) {
            $('#' + cachedModalId).modal('show');
        }

        $('.modal').on('shown.bs.modal', function (e) {
            // Cache the ID of the opened modal
            localStorage.setItem('modalId', e.target.id);
        });

        $('.modal').on('hidden.bs.modal', function () {
            // Remove cached modal ID when the modal is closed
            localStorage.removeItem('modalId');
        });
    });

    function showAlert(message, type = 'success') {
        const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
        </div>`;
        const alertElement = $(alertHtml).appendTo('body').hide().fadeIn();

        setTimeout(() => {
            alertElement.fadeOut(() => {
                alertElement.remove();
            });
        }, 3000); // 3 seconds delay
    }
    // Define deleteViolation globally
    function deleteViolation(violationId, index) {
        // Show confirmation prompt using Toastr
        toastr.options = {
            closeButton: false,
            progressBar: true,
            positionClass: 'toast-top-center',
            preventDuplicates: true,
            onclick: null,
            showDuration: '300',
            hideDuration: '1000',
            timeOut: '0', // To make it sticky
            extendedTimeOut: '0', // To make it sticky
            showEasing: 'swing',
            hideEasing: 'linear',
            showMethod: 'fadeIn',
            hideMethod: 'fadeOut'
        };

        var confirmationPrompt = `
            <div class="confirmation-prompt">
                <p class="prompt-text">Are you sure you want to delete this violation?</p>
                <div class="btn-group">
                    <button type="button" class="btn btn-danger btn-confirm-yes">Yes</button>
                    <button type="button" class="btn btn-secondary btn-confirm-no">No</button>
                </div>
            </div>
        `;

        toastr.info(confirmationPrompt, 'Confirm Deletion', {
            closeButton: true, // To show a close button
            closeHtml: '<button><i class="fas fa-times"></i></button>', // Custom HTML for the close button
        });

        // Store violationId and index as data attributes
        $('.btn-confirm-yes').data('violation-id', violationId);
        $('.btn-confirm-yes').data('index', index);

        // Add event listener for the "Yes" button
        $(document).off('click', '.btn-confirm-yes').on('click', '.btn-confirm-yes', function() {
        deleteViolationRequest(violationId, index);
        toastr.clear(); // Clear the Toastr notification
        });

        $(document).off('click', '.btn-confirm-no').on('click', '.btn-confirm-no', function() {
            toastr.clear(); // Clear the Toastr notification
        });
    }

    // Function to handle deleting a violation
    function deleteViolationRequest(violationId, index) {
        var fetchUrl = '{{ route("edit.viodelete", ["id" => "ID_PLACEHOLDER"]) }}'.replace('ID_PLACEHOLDER', violationId);

        $.ajax({
            type: 'DELETE',
            url: fetchUrl,
            data: {
                _token: '{{ csrf_token() }}',
                index: index
            },
            success: function(response) {
                // reloadModalContent(violationId);
                $('#violationField' + violationId + '_' + index).remove();
                toastr.success(response.message); // Display success message
                 // Remove the deleted violation from the DOM
                 
            },
            error: function(xhr, status, error) {
                toastr.error(xhr.responseJSON.message); // Display error message
            }
        });
    }

    // Rest of your script remains the same

    $(document).ready(function() {
        // Event listener for delete violation button
        $(document).on('click', '.delete-violation', function() {
            var violationId = $(this).data('violation-id');
            var index = $(this).data('index');
            deleteViolation(violationId, index);
        });

        // Event listener for delete attachment button
        $(document).on('click', '.delete-attachment', function() {
            var violationId = $(this).data('violation-id');
            var attachmentToRemove = $(this).data('attachment');
            var index = $(this).data('index');
            deleteAttachment(violationId, attachmentToRemove, index);
        });

        // Function to handle deleting an attachment
        function deleteAttachment(violationId, attachmentToRemove, index) {
            // Show confirmation prompt using Toastr
            toastr.options = {
                closeButton: false,
                progressBar: true,
                positionClass: 'toast-top-center',
                preventDuplicates: true,
                onclick: null,
                showDuration: '300',
                hideDuration: '1000',
                timeOut: '0', // To make it sticky
                extendedTimeOut: '0', // To make it sticky
                showEasing: 'swing',
                hideEasing: 'linear',
                showMethod: 'fadeIn',
                hideMethod: 'fadeOut'
            };

            var confirmationPrompt = `
                <div class="confirmation-prompt">
                    <p class="prompt-text">Are you sure you want to delete this attachment?</p>
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger btn-confirm-yes">Yes</button>
                        <button type="button" class="btn btn-secondary btn-confirm-no">No</button>
                    </div>
                </div>
            `;

            toastr.info(confirmationPrompt, 'Confirm Deletion', {
                closeButton: true, // To show a close button
                closeHtml: '<button><i class="fas fa-times"></i></button>', // Custom HTML for the close button
            });

            // Store violationId and attachmentToRemove as data attributes
            $('.btn-confirm-yes').data('violation-id', violationId);
            $('.btn-confirm-yes').data('attachment', attachmentToRemove);
            $('.btn-confirm-yes').data('index', index);

            // Unbind previously bound event handlers to prevent multiple executions
    $(document).off('click', '.btn-confirm-yes').on('click', '.btn-confirm-yes', function() {
        deleteAttachmentRequest(violationId, attachmentToRemove, index);
        toastr.clear(); // Clear the Toastr notification
    });

    $(document).off('click', '.btn-confirm-no').on('click', '.btn-confirm-no', function() {
        toastr.clear(); // Clear the Toastr notification
    });
        }

        // Function to send AJAX request to delete attachment
        function deleteAttachmentRequest(violationId, attachmentToRemove, index) {
            var fetchUrl = '{{ route("tasfile.removeAttachment", ["id" => "ID_PLACEHOLDER"]) }}'.replace('ID_PLACEHOLDER', violationId);

            $.ajax({
                type: 'DELETE',
                url: fetchUrl,
                data: {
                    _token: '{{ csrf_token() }}',
                    attachment: attachmentToRemove
                },
                success: function(response) {
                    if (response.success) {
                        $('#attachment_row_' + violationId + '_' + index).remove();
                        toastr.success(response.success);
                        // Remove the deleted attachment from the DOM
                        // reloadModalContent(violationId);
                    } else {
                        toastr.error(response.error);
                    }
                },
                error: function(xhr, status, error) {
                    toastr.error('Failed to remove attachment.');
                    console.error('Error:', error);
                }
            });
        }
    });

    function deleteRemark(violationId, index) {
    $.ajax({
        url: '{{ route('edit.deleteremarks') }}',
        type: 'POST', // Change POST to PUT
        data: {
            violation_id: violationId,
            index: index,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            // Remove the HTML element of the deleted remark
            $('#remark_row_' + violationId + '_' + index).remove();
            // Show success notification using Toastr
            toastr.success(response.success);
        },
        error: function(xhr, status, error) {
            // Handle the error
            console.error(xhr.responseText);
            // Show error notification using Toastr
            toastr.error('An error occurred while deleting the remark');
        }
    });
    }
</script>


    
  </main><!-- End #main -->

 @include('layouts.footer')
</body>

</html>
