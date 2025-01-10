
@section('title', env('APP_NAME'))

@include('layouts.title')

<body>

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

    <section class="section">
      <div class="card">
            <div class="card-body">
                <h5 class="card-title">Traffic Adjudication Service<span></span></h5>
                <table class="table table-borderless datatable">
                    <!-- Table header -->
                    <thead class="thead-light">
                        <tr>
                            <th>Record Status</th>
                            <th>Case No</th>
                            <th>Transaction No</th>
                            <th>Top</th>
                            <th>Driver</th>
                            <th>Apprehending Officer</th>
                            <th>Department</th>
                            <th>Plate No.</th>
                            <th>Type of Vehicle</th>
                            <th>Case Status</th>

                        </tr>
                    </thead>
                    <!-- Table body -->
                    <tbody>

                            @foreach ($tasFiles as $tasFile)
                                <tr class="table-row" data-bs-toggle="modal" data-bs-target="#exampleModal{{ $tasFile->id }}">
                                <td class="symbol-cell {{ symbolBgColor($tasFile->symbols) }}" onclick="openModal('{{ $tasFile->symbols }}')">
                                    @if($tasFile->symbols === 'complete')
                                        <span class="text-white"><i class="bi bi-check-circle-fill"></i> Complete</span>
                                    @elseif($tasFile->symbols === 'incomplete')
                                        <span class="text-white"><i class="bi bi-exclamation-circle-fill"></i> Incomplete</span>
                                    @else
                                        <span class="text-white"><i class="bi bi-question-circle-fill"></i> Incomplete</span>
                                    @endif
                                </td>

                                <td>{{ $tasFile->case_no  ?? 'N/A' }}</td>
                                <td>{{ $tasFile->transaction_no ?? 'N/A' }}</td>
                                <td>{{ $tasFile->top ?? 'N/A' }}</td>
                                <td>{{ $tasFile->driver  ?? 'N/A' }}</td>
                                <td>{{ $tasFile->apprehending_officer ?? 'N/A' }}</td>
                                <td>
                                    @if ($tasFile->relatedofficer)
                                        @foreach ($tasFile->relatedofficer as $officer)
                                            {{$officer->department  ?? 'N/A' }}
                                        @endforeach
                                    @endif
                                </td>
                                <td>{{ $tasFile->plate_no  ?? 'N/A' }}</td>
                                <td>{{ $tasFile->typeofvehicle  ?? 'N/A' }}</td>

                                <td style="background-color: {{ getStatusColor($tasFile->status) }}">
                                    @if($tasFile->status === 'closed')
                                        <span><i class="bi bi-check-circle-fill"></i> Closed</span>
                                    @elseif($tasFile->status === 'in-progress')
                                        <span><i class="bi bi-arrow-right-circle-fill"></i> In Progress</span>
                                    @elseif($tasFile->status === 'settled')
                                        <span><i class="bi bi-check-circle-fill"></i> Settled</span>
                                    @elseif($tasFile->status === 'unsettled')
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
    </section>

{{-- @if (Auth::user()->role == 9 || Auth::user()->role == 2) --}}

@foreach($tasFiles as $tasFile)
<div   class="modal fade" id="exampleModal{{ $tasFile->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <span class="bi bi-folder me-1"></span> Case Details - {{ $tasFile->case_no }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" id="modal-body-{{ $tasFile->id }}">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                Loading...
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="finishModal{{ $tasFile->id }}" tabindex="-1" role="dialog" aria-labelledby="finishModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="finishCaseFormTemplate" action="{{ route('finish.case', ['id' => $tasFile->id]) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="finishModalLabel">Please type the Fine Fee:</h5>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="fine_fee">Fine Fee</label>
                        <input type="number" step="0.01" class="form-control" id="fine_fee" name="fine_fee" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endforeach

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    const fetchViolationUrl = @json(route('fetchingtasfile', ['id' => 'ID_PLACEHOLDER']));

    function initializeModalScripts(modalId) {
        // Remove any existing submit handlers before adding new ones
        $('#modal-body-' + modalId + ' .remarksForm').off('submit').on('submit', function (e) {
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
                            initializeModalScripts(modalId); // Re-initialize scripts for new content
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

        // Handle finish case form submission
        $('#finishCaseFormTemplate').off('submit').on('submit', function (e) {
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

    // Avoid multiple fetch requests by checking if the modal content has been loaded
    const modalCache = {}; // Cache to store fetched content for each modal
    const modalInProgress = {}; // Track if a modal content fetch is in progress

    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('show.bs.modal', async function () {
            const modalId = modal.getAttribute('id').replace('exampleModal', '');
            const modalBody = modal.querySelector('.modal-body');

            // Check if the content is already cached
            if (modalCache[modalId]) {
                // Use cached content if available
                modalBody.innerHTML = modalCache[modalId];
                initializeModalScripts(modalId);
            } else if (!modalInProgress[modalId]) {
                // Prevent multiple simultaneous fetch requests
                modalInProgress[modalId] = true;

                const fetchUrl = fetchViolationUrl.replace('ID_PLACEHOLDER', modalId);
                console.log('Fetching URL: ', fetchUrl);

                try {
                    const response = await fetch(fetchUrl);
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    const html = await response.text();

                    // Cache the fetched content
                    modalCache[modalId] = html;

                    // Set the modal content and re-initialize scripts
                    modalBody.innerHTML = html;
                    initializeModalScripts(modalId);

                    // Attach the Finish Case modal dynamically
                    const finishModalHtml = $('#finishModalTemplate').html();
                    $('#modal-body-' + modalId).append(finishModalHtml);
                    $('#finishCaseFormTemplate').attr('action', '{{ route('finish.case', ['id' => 'modalId']) }}');
                } catch (err) {
                    console.error('Failed to reload modal content', err);
                    modalBody.innerHTML = '<p>Error loading content</p>';
                } finally {
                    modalInProgress[modalId] = false; // Reset the flag after fetch is complete
                }
            }
        });
    });
</script>


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
    document.addEventListener('DOMContentLoaded', function () {
        const uploadForm = document.getElementById('uploadForm');
        const attachmentList = document.getElementById('attachmentList');

        // Ensure form exists before attaching event listener
        if (uploadForm) {
            uploadForm.addEventListener('submit', function (event) {
                event.preventDefault();

                let formData = new FormData(uploadForm);

                // Send AJAX request
                fetch(uploadForm.action, {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    // Check if the response contains a redirectUrl
                    if (data.redirectUrl) {
                        toastr.success('File uploaded successfully!');
                        window.location.href = data.redirectUrl;
                    } else {
                        toastr.error('Invalid response from server.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('An error occurred while uploading the file.');
                });
            });
        } else {
            console.error('Upload form not found.');
        }
    });
</script>


  </main><!-- End #main -->
<!-- Modal for Notification Details -->
<div class="modal fade" id="notificationModal" tabindex="-1" aria-labelledby="notificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 80%;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="notificationModalLabel">Notification Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="notificationDetails"></div>
            </div>
        </div>
    </div>
</div>
<!-- End Modal -->

 @include('layouts.footer')
</body>

</html>
