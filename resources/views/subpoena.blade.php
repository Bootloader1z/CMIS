<!DOCTYPE html>
<html>
<head>
    <title>Filtered Contested Cases - {{ $monthYear }}</title>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="description">
    <meta content="" name="keywords">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <link href="https://fonts.gstatic.com" rel="preconnect">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">

    <style>
        
        @media print {
            .print-container table {
                width: 100%;
                border-collapse: collapse;
                page-break-inside: avoid; /* Avoid splitting table rows across pages */
            }
    
            .print-container th, .print-container td {
                border: 2px solid #ffffff; /* Example border style */
                padding: 8px; /* Example padding */
            }
    
            .print-container thead {
                display: table-header-group; /* Repeat table header on each page */
            }
    
            .headxx {
               text-align: center
            }
            
        }
    </style>
    
    
</head>
<body>
    
    <div class="print-container">
        <div class="headxx">
            <h2>CONTESTED CASES</h2>
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
                            <td>{{ number_format($file->fine_fee, 2) }}</td>
                            <td>{{ $file->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    
        <!-- Page Number Container -->
        <div class="page-number"></div>
    </div>
    
    
</body>

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<!-- Bootstrap JS Bundle (popper.js included) -->
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS -->
<script type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

</html>
