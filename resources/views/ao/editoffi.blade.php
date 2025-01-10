@include('layouts.title')

<!-- Include Header -->
@include('layouts.header')

<!-- Include Sidebar -->
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

<main id="main" class="main">



    <section class="section">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Traffic Adjudication Service  </h5>
                  <!-- Search input -->
            <input  class="form-control mb-2"  type="text" id="searchInput" aria-label="search" placeholder="Search by officer name">
                <table id="officers-table" class="table table-striped table-bordered">
                    <!-- Table header -->
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">Apprehending Officer</th>
                            <th scope="col">Department</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <!-- Table body -->
                    <tbody>
                        @foreach ($officers as $officer)
                        <tr data-bs-toggle="modal" data-bs-target="#exampleModal{{ $officer->id }}">
                            <td>{{ $officer->officer ?? 'N/A' }}</td>
                            <td>{{ $officer->department ?? 'N/A'  }}</td>
                            @if ($officer->isactive == 1)
                            <td>Active</td>
                            @else
                            <td>Inactive</td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @foreach ($officers as $officer)
<div class="modal fade" id="exampleModal{{ $officer->id }}" tabindex="-1" aria-labelledby="exampleModalLabel{{ $officer->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel{{ $officer->id }}">Details for {{ $officer->officer ?? 'N/A' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-body-{{ $officer->id }}">
                <!-- Placeholder content -->
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div><strong>Loading...</strong>
            </div>
        </div>
    </div>
</div>


@endforeach

</main>

<script>
    // JavaScript to filter table rows based on input
    document.addEventListener('DOMContentLoaded', function() {
        var searchInput = document.getElementById('searchInput');
        var table = document.getElementById('officers-table');
        var rows = table.getElementsByTagName('tr');

        searchInput.addEventListener('keyup', function() {
            var filter = searchInput.value.toUpperCase();

            // Loop through all table rows, and hide those who don't match the search query
            for (var i = 0; i < rows.length; i++) {
                var officerCell = rows[i].getElementsByTagName('td')[0]; // Assuming first column is officer name
                if (officerCell) {
                    var officerName = officerCell.textContent || officerCell.innerText;
                    if (officerName.toUpperCase().indexOf(filter) > -1) {
                        rows[i].style.display = '';
                    } else {
                        rows[i].style.display = 'none';
                    }
                }
            }
        });
    });
</script>

<script>
    const fetchViolationUrl = @json(route('fetchingofficer', ['id' => 'id']));

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

                // Generate the URL for fetching officer details (replace placeholder)
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

<!-- Initialize DataTables -->
<script>
    $(document).ready(function () {
        $('#officers-table').DataTable();
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

<!-- Loading Screen CSS -->
@include('layouts.footer')

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
</body>

</html>
