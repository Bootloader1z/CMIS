

@section('title', env('APP_NAME'))

@include('layouts.title')

<body>

  <!-- ======= Header ======= -->
@include('layouts.header')

  <!-- ======= Sidebar ======= -->
 @include('layouts.sidebar')

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Case Management Information System</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
{{-- 
<!-- Button to Open the Modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#announcementModal">
    Create Announcement
</button> --}}

<!-- The Modal -->
<div class="modal fade" id="announcementModal" tabindex="-1" aria-labelledby="announcementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="announcementModalLabel">Create Announcement</h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="announcementForm" action="{{ route('announcements.store') }}" method="POST">
                    @csrf

                    <div class="form-group mb-3">
                        <label for="title">Title</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                    </div>

                    <div class="form-group mb-3">
                        <label for="content">Content</label>
                        <textarea name="content" id="content" class="form-control" rows="4" required>{{ old('content') }}</textarea>
                    </div>

                    <div class="form-group mb-3">
                        <label for="is_active">Status</label>
                        <select name="is_active" id="is_active" class="form-control" required>
                            <option value="1" {{ old('is_active') == 1 ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">Create Announcement</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery if not already included -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

<script>
    $(document).ready(function () {
        $('#announcementForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: $(this).attr('action'),
                method: $(this).attr('method'),
                data: $(this).serialize(),
                dataType: 'json',
                success: function (data) {
                    if (data.success) {
                        alert('Announcement created successfully!');
                        $('#announcementModal').modal('hide');
                        $('#announcementForm')[0].reset();
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                }
            });
        });
    });
</script>

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-8">
          <div class="row">
<!-- Clickable Sales Card -->
<div class="col-xxl-4 col-md-6">
    <div class="card info-card sales-card clickable-card" data-bs-toggle="modal" data-bs-target="#salesModal">
        <div class="card-body">
            <h5 class="card-title">{{ date('l') }} <span> | Violations Today</span></h5> <!-- Display today's date -->
            <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-calendar3"></i>
                </div>
                <div class="ps-3">
                    @if($salesToday->isEmpty())
                        <p>No Violations recorded today.</p>
                    @else
                        <h6>{{ $salesToday->count() }}</h6> <!-- Display count of violations for today -->
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Clickable Sales Card -->
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


<div class="modal fade" id="salesModal" tabindex="-1" aria-labelledby="salesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="salesModalLabel">Traffic Violations</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="dateForm" class="mb-3">
                    <div class="row">
                        <div class="col-md-8">
                            <label for="violationDate" class="form-label">Select Date</label>
                            <input type="date" class="form-control" id="violationDate" name="date">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">Get Violations</button>
                        </div>
                    </div>
                </form>
                <div id="violationsContent">
                    @if($salesToday->isEmpty())
                        <p>No traffic violations recorded today.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Case Number</th>
                                        <th>Driver</th>
                                        <th>Plate Number</th>
                                        <th>Violation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($salesToday as $violation)
                                        <tr>
                                            <td>{{ $violation->case_no }}</td>
                                            <td>{{ $violation->driver }}</td>
                                            <td>{{ $violation->plate_no }}</td>
                                            <td>{{ $violation->violation }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('dateForm').addEventListener('submit', function(e) {
        e.preventDefault();
    
        var date = document.getElementById('violationDate').value;
    
        fetch('{{ route("get.today") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ date: date })
        })
        .then(response => response.json())
        .then(data => {
            var violationsContent = document.getElementById('violationsContent');
            if (data.violations.length === 0) {
                violationsContent.innerHTML = '<p>No traffic violations recorded on this date.</p>';
            } else {
                var tableContent = `
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Case Number</th>
                                <th>Driver</th>
                                <th>Plate Number</th>
                                <th>Violation</th>
                            </tr>
                        </thead>
                        <tbody>
                `;
                data.violations.forEach(violation => {
                    tableContent += `
                        <tr>
                            <td>${violation.case_no}</td>
                            <td>${violation.driver}</td>
                            <td>${violation.plate_no}</td>
                            <td>${violation.violation}</td>
                        </tr>
                    `;
                });
                tableContent += `
                        </tbody>
                    </table>
                `;
                violationsContent.innerHTML = tableContent;
            }
        })
        .catch(error => console.error('Error:', error));
    });
    </script>
    
    



<!-- Revenue Card -->
<div class="col-xxl-4 col-md-6">
    <div class="card info-card revenue-card clickable-card" data-bs-toggle="modal" data-bs-target="#revenueModal">
        <div class="card-body">
            <h5 class="card-title">{{ date('F') }} <span> | This Month</span></h5> <!-- Display current month name -->
            <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-calendar2-week-fill"></i>
                </div>
                <div class="ps-3">
                    <h6>{{ $revenueThisMonth }}</h6> <!-- Display revenue for this month -->
                    {{-- @if($previousMonthRevenue > 0)
                    @php
                        $percentageChange = (($revenueThisMonth - $previousMonthRevenue) / $previousMonthRevenue) * 100;
                    @endphp
                    <span class="text-muted small pt-2">({{ $percentageChange > 0 ? '+' : '' }}{{ number_format($percentageChange, 2) }}%)</span>
                    @endif --}}
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Revenue Card -->


<!-- Revenue Modal -->
<div class="modal fade" id="revenueModal" tabindex="-1" aria-labelledby="revenueModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="revenueModalLabel">Record Count by Month</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Month</th>
                            <th>Record Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($countByMonth as $count)
                        <tr>
                            <td>{{ Carbon\Carbon::create()->month($count['month'])->format('F') }}</td>
                            <td>{{ $count['record_count'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- End Revenue Modal -->




<!-- Customers Card -->
<div class="col-xxl-4 col-xl-12">
    <div class="card info-card customers-card clickable-card" data-bs-toggle="modal" data-bs-target="#customersModal">
        <div class="card-body">
            <h5 class="card-title">{{ date('Y') }}<span> | This Year</span></h5> <!-- Display current year -->
            <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                    <i class="bi bi-calendar-range-fill"></i>
                </div>
                <div class="ps-3">
                    <h6>{{ $customersThisYear }}</h6> <!-- Display customers for this year -->
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Customers Card -->

<!-- Customers Modal -->
<div class="modal fade" id="customersModal" tabindex="-1" aria-labelledby="customersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="customersModalLabel">Yearly Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Record Count</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($yearlyData as $year => $record)
                            <tr>
                                <td><a href="{{ route('showYearData', $year) }}">{{ $year }}</a></td>
                                <td>{{ $record->record_count }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<!-- End Customers Modal -->




          <!-- Website Traffic -->
          <div class="col-12">
          <div class="card">
          

            <div class="card-body pb-0">
            <h5 class="card-title">Concerned Apprehending Offices <span id="todaySpan">| Chart</span></h5>

            <div id="trafficChart" style="min-height: 400px;" class="echart"></div>
<!-- Modal -->
<div class="modal fade" id="departmentModal" tabindex="-1" aria-labelledby="departmentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="departmentModalLabel">Officers from <span id="modalDepartmentName"></span> Department</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="modalBody">
        <!-- Officer names will be displayed here -->
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", () => {
    var trafficChart = echarts.init(document.querySelector("#trafficChart"));

    // Assuming $departmentsData is available as a JavaScript variable.
    var departmentsData = {!! json_encode($departmentsData) !!};

    // Process the data to calculate the total count for each department
    var departmentCounts = {};
    departmentsData.forEach(function(department) {
        if (department.department in departmentCounts) {
            departmentCounts[department.department]++;
        } else {
            departmentCounts[department.department] = 1;
        }
    });

    // Convert the departmentCounts object into an array of objects
    var data = Object.keys(departmentCounts).map(function(key) {
        return { name: key, value: departmentCounts[key] };
    });

    // Render the chart
    trafficChart.setOption({
        tooltip: {
            trigger: 'item'
        },
        legend: {
        orient: 'vertical',
        left: '5%',
        top: 'center',
    },
        series: [{
            name: 'Total Officers From:',
            type: 'pie',
            radius: ['45%', '95%'],
            avoidLabelOverlap: false,
            label: {
                show: false,
                position: 'center'
            },
            emphasis: {
                label: {
                    show: true,
                    fontSize: '18',
                    fontWeight: 'bold'
                }
            },
            labelLine: {
                show: false
            },
            data: data
        }]
    });

    // Add event listener to chart
    trafficChart.on('click', function (params) {
      if (params.componentType === 'series') {
        // Fetch officers from the clicked department
        var departmentName = params.name;
        fetch('/officers/' + encodeURIComponent(departmentName))
          .then(response => response.json())
          .then(data => {
            // Populate modal with officer names
            var modalDepartmentName = document.getElementById('modalDepartmentName');
            var modalBody = document.getElementById('modalBody');
            modalDepartmentName.textContent = departmentName;
            modalBody.innerHTML = '';
            data.forEach(officer => {
              var officerName = document.createElement('div');
              officerName.textContent = officer.officer;
              modalBody.appendChild(officerName);
            });
            // Display modal
            var departmentModal = new bootstrap.Modal(document.getElementById('departmentModal'));
            departmentModal.show();
          })
          .catch(error => console.error('Error fetching officers:', error));
      }
    });
  });
</script>


            </div>
          </div><!-- End Website Traffic -->
</div>




          </div>
          <div class="row">
            <div class="col-12">
                <div class="card recent-sales overflow-auto">
                    <div class="card-body">
                        <h5 class="card-title">Recent Encode <span>| Today</span></h5>
        
                        <div class="datatable-wrapper datatable-loading no-footer sortable searchable fixed-columns">
                            <div class="datatable-container">
                                <table id="recentSalesTable" class="table table-striped table-bordered">
                                    <thead class="thead-dark">
                                        <tr>
                                            <th scope="col">Case/Admitted No</th>
                                            <th scope="col">Driver</th>
                                            <th scope="col">Violation</th>
                                            <th scope="col">Fine</th>
                                            <th scope="col">Case</th>
                                            <th scope="col">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Loop through TasFile data -->
                                        @foreach ($recentSalesTodayTasFile as $sale)
                                        <tr>
                                            <td>{{ $sale->case_no }}</td>
                                            <td>{{ $sale->driver }}</td>
                                            <td>{{ $sale->violation }}</td>
                                            <td>{{ $sale->fine_fee }}</td>
                                            <td style="font-weight: bold; color: #c22323;">Case Contested</td>
                                            <td style="background-color: {{ getStatusColor($sale->status) }};">
                                                <strong>{{ $sale->status }}</strong>
                                            </td>
                                        </tr>
                                        @endforeach
        
                                        <!-- Loop through CaseAdmitted data -->
                                        @foreach ($recentSalesTodayCaseAdmitted as $sale)
                                        <tr>
                                            <td>{{ $sale->admittedno }}</td>
                                            <td>{{ $sale->driver }}</td>
                                            <td>{{ $sale->violation }}</td>
                                            <td>{{ $sale->fine_fee }}</td>
                                            <td style="font-weight: bold; color: #007bff;">Case Admitted</td>
                                            <td style="background-color: {{ getStatusColor($sale->status) }}; font-weight: bold;">
                                                {{ $sale->status }}
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="datatable-bottom">
                                <!-- Pagination for TasFile (adjust as per your actual pagination variable) -->
                                {{ $recentSalesTodayTasFile->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        <!-- JavaScript for DataTables initialization and search functionality -->
        <script>
            $(document).ready(function() {
                // Initialize DataTable
                var table = $('#recentSalesTable').DataTable({
                    "paging": false, // Disable pagination (since you're using Laravel pagination separately)
                    "searching": true, // Enable searching
                    "info": false, // Disable info text showing "Showing 1 to X of X entries"
                });
        
                // Add search functionality
                $('#searchInput').on('keyup', function() {
                    table.search($(this).val()).draw();
                });
            });
        </script>
        
        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        <div class="col-lg-4">
         
        
                <div class="card">
                    <div class="card-header">
                       <h5 class="card-title"> Apprehending Officers Status</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="department">Select Department:</label>
                            <select class="form-control" id="department">
                                <option value="">All Departments</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->department }}">{{ $department->department }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="chart"></div>
                    </div>
                </div>
         
        
                <script>
                    $(document).ready(function() {
                        var chart;
            
                        function fetchChartData(department = '') {
                            $.ajax({
                                url: '{{ route("officers.status") }}',
                                method: 'GET',
                                data: { department: department },
                                success: function(response) {
                                    console.log('Fetched Data:', response); // Log the response data to ensure it's correct
            
                                    // Check if both active and inactive counts are being fetched correctly
                                    var inactiveCount = response.inactive !== undefined ? response.inactive : 0;
                                    var activeCount = response.active !== undefined ? response.active : 0;
            
                                    var options = {
                                        chart: {
                                            type: 'pie'
                                        },
                                        series: [inactiveCount, activeCount],
                                        labels: ['Inactive', 'Active']
                                    };
            
                                    if (chart) {
                                        chart.destroy();
                                    }
            
                                    chart = new ApexCharts(document.querySelector("#chart"), options);
                                    chart.render();
                                },
                                error: function(xhr, status, error) {
                                    console.error('AJAX Error:', error);
                                }
                            });
                        }
            
                        // Fetch initial chart data
                        fetchChartData();
            
                        // Fetch chart data on department change
                        $('#department').change(function() {
                            var department = $(this).val();
                            fetchChartData(department);
                        });
                    });
                </script>
    <!-- Recent Activity -->
    <div class="card">
        
        <div class="card-body">
            <h5 class="card-title">User Activity <span>| Today</span></h5>

            <div id="activity-container" class="activity">
                <!-- Activity items will be appended here -->
            </div>
        </div>
    </div><!-- End Recent Activity -->



    <script>
        $(document).ready(function() {
            function fetchRecentActivity() {
                $.ajax({
                    url: '{{ route("api.recentActivity") }}',
                    method: 'GET',
                    success: function(response) {
                        var activityContainer = $('#activity-container');
    
                        // Clear previous activities only if there are new activities
                        if (response.length > 0) {
                            activityContainer.empty(); // Clear previous activities
                        }
    
                        var index = 0;
                        setInterval(function() {
                            var activity = response[index];
                            var fieldContent = '';

// Determine the content of the Field based on conditions
if (activity.field == 'symbols') {
    fieldContent = 'Record Status';
} else if (activity.file_attach) {
    fieldContent = 'File Attachment';
} else if (activity.typeofvehicle) {
    fieldContent = 'Type of Vehicle';
} else {
    fieldContent = activity.field;
}

                            var activityItem = `
                           <div class="activity-item d-flex">
    <div class="activity-label">${new Date(activity.created_at).toLocaleString()}</div>
    <i class="bi bi-clock-fill activity-badge text-success align-self-start"></i>
    <div class="activity-content">
        <h5> ${activity.model}</h5>
        <p><strong>Description:</strong> ${activity.description}</p>
          <p><strong>Field:</strong> ${fieldContent}</p>
        <p><strong>By:</strong> ${activity.user.fullname} (${activity.user.username})</p>
    </div>
</div><!-- End activity item -->

                                <hr> <!-- Separator -->
                            `;
                            activityContainer.html(activityItem);
                            activityContainer.children().fadeIn(500); // Fade in the activity
    
                            index = (index + 1) % response.length; // Move to the next activity cyclically
                        }, 3000); // Repeat every 3 seconds (adjust as needed)
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching recent activity:', error);
                    }
                });
            }
    
            // Fetch initial activity data
            fetchRecentActivity();
        });
    </script>
    
        </div><!-- End Right side columns -->
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="card top-selling overflow-auto">
                        <div class="card-body pb-0">
                            <h5 class="card-title">Top Apprehending Officers <span>| Cases</span></h5>
                            <!-- Filter buttons -->
                            <div class="mb-3">
                                <div class="input-group">
                                    <input type="text" id="searchInput" class="form-control" placeholder="Search by details...">
                                    <label for="limitSelect" class="input-group-text">Show Entries</label>
                                    <select id="limitSelect" class="form-select form-select">
                                        <option value="10">10</option>
                                        <option value="50">50</option>
                                        <option value="100">100</option>
                                    </select>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table id="officers-table" class="table table-striped table-hover custom-table">
                                    <thead>
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Apprehending Officer</th>
                                            <th scope="col">Department</th>
                                            <th scope="col">Total Cases</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($officers as $index => $officer)
                                        <tr class="{{ $index < 3 ? 'top-officer' : '' }}">
                                            <th scope="row">{{ $index + 1 }}</th>
                                            <td>
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#officerModal{{ $index }}">{{ $officer->apprehending_officer ?: 'Unknown' }}</a>
                                            </td>
                                            <td>{{ $officer->department }}</td>
                                            <td>{{ $officer->total_cases ?: 'Unknown' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Modals for Officer Details -->
        @if($officers->isNotEmpty())
        @foreach($officers as $index => $officer)
        <div class="modal fade" id="officerModal{{ $index }}" tabindex="-1" aria-labelledby="officerModalLabel{{ $index }}" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Cases Handled by {{ $officer->apprehending_officer ?: 'Unknown' }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Case No</th>
                                    <th>Driver</th>
                                    <th>Violation</th>
                                    <th>Date Received</th>
                                    <th>Contact No</th>
                                    <th>Plate No</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                $caseNumbers = $officer->case_numbers ? explode(',', $officer->case_numbers) : [];
                                @endphp
                                @foreach($caseNumbers as $caseNo)
                                @php
                                $case = App\Models\TasFile::where('case_no', $caseNo)->first();
                                @endphp
                                <tr>
                                    <td>{{ $caseNo }}</td>
                                    <td>{{ $case ? $case->driver : 'Unknown' }}</td>
                                    <td>{{ $case ? $case->violation : 'Unknown' }}</td>
                                    <td>{{ $case ? $case->date_received : 'Unknown' }}</td>
                                    <td>{{ $case ? $case->contact_no : 'Unknown' }}</td>
                                    <td>{{ $case ? $case->plate_no : 'Unknown' }}</td>
                                </tr>
                                @endforeach
                                @if(empty($caseNumbers))
                                <tr>
                                    <td colspan="6">No case numbers found.</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @else
        <p>No officers found.</p>
        @endif
        
        <script>
            $(document).ready(function() {
                // Search functionality
                $('#searchInput').on('keyup', function() {
                    let searchText = $(this).val().toLowerCase();
                    $('#officers-table tbody tr').filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(searchText) > -1);
                    });
                });
        
                // Change the limit of officers displayed
                $('#limitSelect').on('change', function() {
                    let limit = $(this).val();
                    window.location.href = "{{ route('dashboard') }}?limit=" + limit;
                });
            });
        </script>
        

      </div>
    </section>

  </main><!-- End #main -->

 @include('layouts.footer')
</body>

</html>