<!DOCTYPE html>
<html>
<head>
    <title>Reports Contested Cases - {{ $monthYear }}</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="{{ asset('assets/css/carcar.css') }}" rel="stylesheet">
    <link href="{{asset('assets/img/logo.png')}}" rel="icon">
    <link href="{{asset('assets/img/logo.png')}}" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{asset('assets/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
    <link href="{{asset('assets/vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/vendor/quill/quill.snow.css')}}" rel="stylesheet">
    <link href="{{asset('assets/vendor/quill/quill.bubble.css')}}" rel="stylesheet">
    <link href="{{asset('assets/vendor/remixicon/remixicon.css')}}" rel="stylesheet">
    <link href="{{asset('assets/vendor/simple-datatables/style.css')}}" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="{{asset('assets/css/style.css')}}" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <style>
        @media print {
            thead { display: table-header-group; }
        }
        @page {
            size: landscape;
        }
        .headxx {
            text-align: center;
        }
    </style>
    
    
</head>
<body>
    
    <div class="print-container">
        <div class="headxx">
            <h1>𝓒𝓞𝓝𝓣𝓔𝓢𝓣𝓔𝓓 𝓒𝓐𝓢𝓔𝓢</h1>
                <span>{{$monthYear}}</span>
        </div>
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Driver</th>
                        <th>Case No.</th>
                        <th>Violation</th>
                        <th>Plate No.</th>
                        <th>Fine Fee</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasFiles as $index => $file)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $file->driver }}</td>
                            <td>{{ $file->case_no }}</td>
                            <td>
                                @if ($file->relatedViolations->isNotEmpty())
                                    @foreach ($file->relatedViolations as $violation)
                                        {{ strtoupper($violation->violation) }}<br>
                                    @endforeach
                                @else
                                    No Violations
                                @endif
                            </td>
                            <td>{{ is_array($file->plate_no) ? implode(', ', $file->plate_no) : $file->plate_no }}</td>

                            <td contenteditable="true" data-fine="partial_total">
                                @if ($file->fine_fee)
                                    {{ number_format($file->fine_fee, 2) }}
                                @else
                                    {{-- @foreach ($file->relatedViolations as $violation)
                                    {{ strtoupper(number_format($violation->fine, 2)) }}<br>
                                    @endforeach --}}

                                    {{ number_format($file->partialFinePerFile, 2) }}
                                @endif
                            </td>
                            <td>{{ $file->status }}</td>
                        </tr>
                        
                    @endforeach
                </tbody>
            </table>

            <h4 id="totalFine" style="text-align: right; display: block; margin-right: 7vh;font-weight: bolder;">Total:</h4>
        </div>
    
        <!-- Page Number Container -->
        <div class="page-number"></div>
    </div>
    
    
</body>
<!-- Ensure the 'number_format' function is defined before this script block -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fineCells = document.querySelectorAll('td[data-fine="partial_total"]');
        const totalFineSpan = document.getElementById('totalFine');
    
        // Function to calculate total fine amount
        function updateTotalFine() {
            let total = 0;
    
            fineCells.forEach(cell => {
                const fineAmount = parseFloat(cell.textContent.replace(/,/g, ''));
                if (!isNaN(fineAmount)) {
                    total += fineAmount;
                }
            });
    
            totalFineSpan.textContent = "Total: " + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }
    
        // Event listener for input in each fine cell
        fineCells.forEach(cell => {
            cell.addEventListener('input', function() {
                updateTotalFine();
            });
        });
    
        // Initial calculation on page load
        updateTotalFine();
    });
    </script>
    
<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Bootstrap JS Bundle (popper.js included) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

</html>
