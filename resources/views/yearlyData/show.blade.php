

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
    <div class="container-fluid"> <!-- Use container-fluid for full width -->
        <div class="card">
            <div class="card-header">
                <h1>Data for Year {{ $year }}</h1>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search...">
                </div>
                <div class="table-responsive">
                    <table class="table" id="dataTable">
                        <thead>
                            <tr>
                        
                                <th>Case No</th>
                                <th>Top</th>
                                <th>Driver</th>
                                <th>Apprehending Officer</th>
                                <th>Violation</th>
                                <th>Transaction No</th>
                                <th>Date Received</th>
                                <th>Contact No</th>
                                <th>Plate No</th>
                                <th>Remarks</th>
                           
                                <th>Type of Vehicle</th>
                                <th>Fine Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($data as $record)
                            <tr>
                           
                                <td>{{ $record->case_no }}</td>
                                <td>{{ $record->top }}</td>
                                <td>{{ $record->driver }}</td>
                                <td>{{ $record->apprehending_officer }}</td>
                                <td>{{ $record->violation }}</td>
                                <td>{{ $record->transaction_no }}</td>
                                <td>{{ $record->date_received }}</td>
                                <td>{{ $record->contact_no }}</td>
                                <td>{{ $record->plate_no }}</td>
                                <td>{{ $record->remarks }}</td>
                              
                                <td>{{ $record->typeofvehicle }}</td>
                                <td>{{ $record->fine_fee }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer">
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Back to Dashboard</a>
            </div>
        </div>
    </div>
</main>

<script>
    $(document).ready(function() {
        $('#searchInput').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#dataTable tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>



 @include('layouts.footer')
</body>

</html>
