@include('layouts.title')

@include('layouts.header')
@include('layouts.sidebar')
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



<!-- Loading Screen Script -->
<main id="main" class="main">
    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Traffic Adjudication Service</h5>
                <table id="violations-table" class="table table-striped table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Violation Code</th>
                            <th scope="col">Violation Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($violations as $violation)
                        <tr data-bs-toggle="modal" data-bs-target="#exampleModal{{ $violation->id }}">
                            <td>{{ $violation->code ?? 'N/A' }}</td>
                            <td>{{ $violation->violation }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</main>

@foreach ($violations as $violation)
<div class="modal fade" id="exampleModal{{ $violation->id }}" tabindex="-1" aria-labelledby="exampleModalLabel{{ $violation->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel{{ $violation->id }}">Details for {{ $violation->violation ?? 'N/A' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-body-{{ $violation->id }}">

                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div><strong>Loading...</strong>
            </div>

        </div>
    </div>
</div>
@endforeach

@include('layouts.footer')

<!-- Scripts at the end of body -->
<script>
    const fetchViolationUrl = @json(route('fetchingviolation', ['id' => 'id']));

    // Cache to store modal content and in-progress status
    const modalCache = {};
    const modalInProgress = {};

    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget; // Button that triggered the modal
            var modalId = modal.getAttribute('id').replace('exampleModal', '');
            var modalBody = modal.querySelector('.modal-body');

            // Check if the modal content is already cached
            if (modalCache[modalId]) {
                // If content is cached, use it directly
                modalBody.innerHTML = modalCache[modalId];
            } else if (!modalInProgress[modalId]) {
                // If a request is not in progress, start a new fetch request
                modalInProgress[modalId] = true;

                // Generate the URL for fetching violation details (replace placeholder)
                var fetchUrl = fetchViolationUrl.replace('id', modalId);
                console.log('Fetching URL: ', fetchUrl);

                // Delay the fetch request by 1.5 seconds
                setTimeout(() => {
                    // Fetch content for the modal via AJAX or a fetch request
                    fetch(fetchUrl)
                        .then(response => response.text())
                        .then(html => {
                            // Cache the fetched content
                            modalCache[modalId] = html;

                            // Set the modal content
                            modalBody.innerHTML = html;
                        })
                        .catch(err => {
                            console.error('Failed to load modal content', err);
                            modalBody.innerHTML = '<p>Error loading content</p>';
                        })
                        .finally(() => {
                            // Reset the in-progress flag after fetch is complete
                            modalInProgress[modalId] = false;
                        });
                }, 1500); // 1.5 seconds delay
            }
        });
    });
</script>



<script>
    // Initialize DataTable
    $(document).ready(function() {
        $('#violations-table').DataTable();
    });
</script>
<!-- Bootstrap CSS -->


<!-- DataTables CSS -->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Bootstrap JS Bundle (popper.js included) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
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
<!-- Loading Screen CSS -->
</body>
</html>
