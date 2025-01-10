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
                <h5 class="card-title"><i class="bi bi-check2-square"></i> Admitted Cases | <i class="bi bi-eye-fill"></i> View<span></span></h5>
                <table class="table table-borderless datatable">
                    <!-- Table header -->
                    <thead class="thead-light">
                        <tr>
                            <th>Record Status</th>
                            <th>Admitted No</th>
                            <th>Transaction No</th>
                            <th>Top</th>
                            <th>Driver</th>
                            <th>Apprehending Officer</th>
                            <th>Department</th>
                            <th>Type of Vehicle</th>
                            <th>Date Received</th>
                            <th>Plate No.</th>
                            <th>Case Status</th>

                        </tr>
                    </thead>
                    <!-- Table body -->
                    <tbody>
                        @foreach ($admitted as $admit)
                        <tr class="table-row" data-bs-toggle="modal" data-bs-target="#exampleModal{{ $admit->id }}">
                        <td class="symbol-cell {{ symbolBgColor($admit->symbols) }}" onclick="openModal('{{ $admit->symbols }}')">
                            @if($admit->symbols === 'complete')
                                <span class="text-white"><i class="bi bi-check-circle-fill"></i> Complete</span>
                            @elseif($admit->symbols === 'incomplete')
                                <span class="text-white"><i class="bi bi-exclamation-circle-fill"></i> Incomplete</span>
                            @elseif($admit->symbols === 'deleting')
                                <span class="text-white"><i class="bi bi-trash-fill"></i> Deleting</span>
                            @else
                                <span class="text-white"><i class="bi bi-question-circle-fill"></i> Incomplete</span>
                            @endif
                        </td>
                            <td>{{ $admit->admittedno  ?? 'N/A' }}</td>
                            <td>{{ $admit->transaction_no ?? 'N/A' }}</td>
                            <td>{{ $admit->top ?? 'N/A' }}</td>
                            <td>{{ $admit->driver  ?? 'N/A' }}</td>
                            <td>{{ $admit->apprehending_officer ?? 'N/A' }}</td>
                            <td>
                                @if ($admit->relatedofficer)
                                    @foreach ($admit->relatedofficer as $officer)
                                        {{$officer->department  ?? 'N/A' }}
                                    @endforeach
                                @endif
                            </td>
                            <td>{{ $admit->plate_no  ?? 'N/A' }}</td>
                            <td>{{ $admit->typeofvehicle  ?? 'N/A' }}</td>
                            <td>{{ $admit->date_received  ?? 'N/A' }}</td>
                            <td style="background-color: {{ getStatusColor($admit->status) }}">
                                @if($admit->status === 'closed')
                                    <span><i class="bi bi-check-circle-fill"></i> Closed</span>
                                @elseif($admit->status === 'in-progress')
                                    <span><i class="bi bi-arrow-right-circle-fill"></i> In Progress</span>
                                @elseif($admit->status === 'settled')
                                    <span><i class="bi bi-check-circle-fill"></i> Settled</span>
                                @elseif($admit->status === 'unsettled')
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
    @foreach($admitted as $admit)
    <div class="modal fade" id="exampleModal{{ $admit->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" style="max-width: 80%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span class="bi bi-folder me-1"></span> Case Details - {{ $admit->admittedno }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body" id="modal-body-{{ $admit->id }}">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    Loading...
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="finishModal{{ $admit->id }}" tabindex="-1" role="dialog" aria-labelledby="finishModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('finishCase_admitted', ['id' => $admit->id]) }}" method="POST"> @csrf <div class="modal-header">
                <h5 class="modal-title" id="finishModalLabel">Finish Case</h5>
            </div>
            <div class="modal-body">
                <div class="form-group">
                <label for="fine_fee">Fine Fee</label>
                <input type="number" step="0.01" class="form-control" id="fine_fee" name="fine_fee" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Finish</button>
            </div>
            </form>
        </div>
        </div>
    </div>
    @endforeach
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @if($admitted=== null)
    @else
    <script>
        const fetchViolationUrl = @json(route('fetchingadmitted', ['id' => 'ID_PLACEHOLDER']));
        const modalCache = {};
        const modalInProgress = {};
        function initializeModalScripts(modalId) {
            $('#modal-body-' + modalId + ' .remarksForm').off('submit').on('submit', function (e) {
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
                    dataType: 'json',
                    success: function (response) {
                        spinner.addClass('d-none');
                        saveRemarksBtn.prop('disabled', false);
                        toastr.success(response.message);

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
                        spinner.addClass('d-none');
                        saveRemarksBtn.prop('disabled', false);
                        showAlert('Failed to save remarks. Please try again later.', 'danger');
                    }
                });
            });

            $('#finishCaseFormTemplate').off('submit').on('submit', function (e) {
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
                    dataType: 'json',
                    success: function (response) {
                        spinner.addClass('d-none');
                        submitBtn.prop('disabled', false);
                        toastr.success(response.message);
                        $('#finishModal' + modalId).modal('hide');
                    },
                    error: function () {

                        spinner.addClass('d-none');
                        submitBtn.prop('disabled', false);
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

            setTimeout(() => {
                alertElement.fadeOut(() => {
                    alertElement.remove();
                });
            }, 3000);
        }

        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('show.bs.modal', async function () {
                const modalId = modal.getAttribute('id').replace('exampleModal', '');
                const modalBody = modal.querySelector('.modal-body');
                if (modalCache[modalId]) {
                    modalBody.innerHTML = modalCache[modalId];
                    initializeModalScripts(modalId);
                } else if (!modalInProgress[modalId]) {
                    modalInProgress[modalId] = true;

                    const fetchUrl = fetchViolationUrl.replace('ID_PLACEHOLDER', modalId);
                    console.log('Fetching URL: ', fetchUrl);

                    try {
                        const response = await fetch(fetchUrl);
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        const html = await response.text();
                        modalCache[modalId] = html;
                        modalBody.innerHTML = html;
                        initializeModalScripts(modalId);
                        const finishModalHtml = $('#finishModalTemplate').html();
                        $('#modal-body-' + modalId).append(finishModalHtml);
                        $('#finishCaseFormTemplate').attr('action', '{{ route('finish.case', ['id' => 'modalId']) }}');
                    } catch (err) {
                        console.error('Failed to reload modal content', err);
                        modalBody.innerHTML = '<p>Error loading content</p>';
                    } finally {
                        modalInProgress[modalId] = false;
                    }
                }
            });
        });
    </script>
    @endif
    <script>
        // Function to open a URL in a new tab and print
        function openInNewTabAndPrint(url) {
            const win = window.open(url, '_blank');
            win.onload = function () {
                win.print();
            };
        }
    </script>
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
    </main><!-- End #main -->

    @include('layouts.footer')
</body>

</html>
