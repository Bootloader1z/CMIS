@section('title', env('APP_NAME'))

@include('layouts.title')

<body>

    <!-- ======= Header ======= -->
    @include('layouts.header')

    <!-- ======= Sidebar ======= -->
    @include('layouts.sidebar')

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Case Admitted History</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"> Home </li>
                    <li class="breadcrumb-item active">Case Admitted History</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
     
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <!-- Filter dropdown and Search -->
                            <div class="card-header">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button"
                                                id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                                Filter
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
                                                <li><a class="dropdown-item" href="{{ route('audit.trail', ['filter' => 'all']) }}">All</a></li>
                                                <li><a class="dropdown-item" href="{{ route('audit.trail', ['filter' => 'today']) }}">Today</a></li>
                                                <li><a class="dropdown-item" href="{{ route('audit.trail', ['filter' => 'this_month']) }}">This Month</a></li>
                                                <li><a class="dropdown-item" href="{{ route('audit.trail', ['filter' => 'this_year']) }}">This Year</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <form class="d-flex">
                                            <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search"
                                                id="searchInput">
                                            <!-- No action attribute to prevent page reload -->
                                            <button class="btn btn-outline-secondary" type="button" id="searchButton">Search</button>
                                            <button class="btn btn-outline-secondary" type="button" id="clearButton">Clear</button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Card body -->
                            <div class="card-body">
                                <h5 class="card-title">Audit Trail | {{ ucfirst($filter) }}</h5>

                                <!-- Table -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="auditTrailTable">
                                        <thead>
                                            <tr>
                                                <th scope="col">Date</th>
                                                <th scope="col">User</th>
                                                <th scope="col">Action</th>
                                                <th scope="col">File Type</th>
                                                <th scope="col">Field</th>
                                                <th scope="col">Old Value</th>
                                                <th scope="col">New Value</th>
                                                <th scope="col">Details</th>
                                                <th scope="col">Description</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($activities as $activity)
                                            <tr data-date="{{ $activity->created_at->format('Y-m-d H:i:s') }}"
                                                data-user="{{ $activity->user ? $activity->user->fullname : 'Unknown User' }}"
                                                data-action="{{ $activity->action }}"
                                                data-file-type="{{ $activity->model === 'App\Models\admitted' ? 'Case Admitted' : $activity->model }}"
                                                data-field="{{ $activity->action !== 'CREATED' ? $activity->field : '-' }}"
                                                data-old-value="{{ $activity->action !== 'CREATED' ? $activity->old_value : '-' }}"
                                                data-new-value="{{ $activity->action !== 'CREATED' ? $activity->new_value : '-' }}"
                                                data-details="{{ $activity->details ? json_encode(json_decode($activity->details, true)) : '{}' }}"
                                                data-description="{{ $activity->description ?? '-' }}">
                                                <td>{{ $activity->created_at->format('Y-m-d H:i:s') }}</td>
                                                <td>{{ $activity->user ? $activity->user->fullname : 'Unknown User' }}</td>
                                                <td>{{ $activity->action }}</td>
                                                <td>
                                                    @if ($activity->model === 'App\Models\admitted')
                                                        Case Admitted
                                                    @elseif ($activity->model === 'App\Models\TasFile')
                                                        Case Contested
                                                    @elseif ($activity->model === 'App\Models\Archives')
                                                        Case Archives
                                                    @elseif ($activity->model === 'App\Models\ApprehendingOfficer')
                                                        Apprehending Officer
                                                    @else
                                                        {{ $activity->model }}
                                                    @endif
                                                </td>
                                                
                                                
                                                <td>{{ $activity->field === 'symbols' ? 'Record Status' : ($activity->action !== 'CREATED' ? str_replace('_', ' ', $activity->field) : '-') }}</td>

                                                <td>
                                                    @if ($activity->action !== 'CREATED')
                                                        @if (is_array($activity->old_value))
                                                            <ol>
                                                                @foreach ($activity->old_value as $value)
                                                                    <li>{{ str_replace(['attachments\/', '\\', '[', ']', '"'], '', $value) }}</li>
                                                                @endforeach
                                                        
                                                        @else
                                                          {{ str_replace(['attachments\/', '\\', '[', ']', '"'], '', $activity->old_value) }}
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </ol>
                                                </td>
                                                <td>
                                                    @if ($activity->action !== 'CREATED')
                                                        @if (is_array($activity->new_value))
                                                            <ol>
                                                                @foreach ($activity->new_value as $value)
                                                                    <li>{{ str_replace(['attachments\/', '\\', '[', ']', '"'], '', $value) }}</li>
                                                                @endforeach
                                                      
                                                        @else
                                                         {{ str_replace(['attachments\/', '\\', '[', ']', '"'], '', $activity->new_value) }}
                                                        @endif
                                                    @else
                                                        -
                                                    @endif
                                                </ol>
                                                </td>
                                                
                                                
                                                
                                                
                                                <td>
                                                    @if ($activity->details)
                                                    @php
                                                    $details = json_decode($activity->details, true);
                                                    @endphp
                                                    <div class="details-list">
                                                        @foreach($details as $key => $value)
                                                        <div class="detail-item">
                                                            <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong>
                                                            {{ in_array($key, ['created_at', 'updated_at']) ? \Carbon\Carbon::parse($value)->format('Y-m-d H:i:s') : $value }}
                                                        </div>
                                                        @endforeach
                                                    </div>
                                                    @endif
                                                </td>
                                                <td>{{ $activity->description ?? '-' }}</td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="9">No activities found.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Pagination links -->
                                <div class="d-flex justify-content-center mt-4">
                                    {{ $activities->links() }}
                                </div>
                            </div><!-- End card body -->
                        </div><!-- End card -->
                    </div><!-- End col-lg-12 -->
                </div><!-- End row -->
         
        </section><!-- End section -->

    </main><!-- End #main -->

    @include('layouts.footer')

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            // Search functionality
            $('#searchInput').on('keyup', function () {
                let searchText = $(this).val().toLowerCase();
                $('#auditTrailTable tbody tr').each(function () {
                    let found = false;
                    $(this).find('td').each(function () {
                        if ($(this).text().toLowerCase().includes(searchText)) {
                            found = true;
                            return false; // break the loop
                        }
                    });
                    $(this).toggle(found);
                });
            });

            // Clear search
            $('#clearButton').click(function () {
                $('#searchInput').val('');
                $('#auditTrailTable tbody tr').show();
            });
        });
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

</body>

</html>