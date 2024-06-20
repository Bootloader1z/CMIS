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
                <h5 class="card-title">Traffic Adjudication Service</h5>
                <table id="deps-table" class="table table-striped table-bordered">
                    <!-- Table header -->
                    <thead class="thead-light">
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Department</th>
                        </tr>
                    </thead>
                    <!-- Table body -->
                    <tbody>
                        @foreach ($deps as $dep)
                        <tr data-bs-toggle="modal" data-bs-target="#exampleModal{{ $dep->id }}">
                            <td>{{ $dep->id ?? 'N/A' }}</td>
                            <td>{{ $dep->department ?? 'N/A'  }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @foreach ($deps as $dep)
<div class="modal fade" id="exampleModal{{ $dep->id }}" tabindex="-1" aria-labelledby="exampleModalLabel{{ $dep->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel{{ $dep->id }}">Details for {{ $dep->department ?? 'N/A' }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modal-body-{{ $dep->id }}">
                <form method="POST" action="{{ route('deps.update', ['id' => $dep->id]) }}">
                    @csrf
                    @method('PUT')
                
                    
                
                    <div class="mb-3">
                        <label for="department" class="form-label">Department</label>
                        <input type="text" class="form-control" id="department" name="department"
                            list="departmentList" value="{{ old('department', $dep->department) }}">
                        
                    </div>
                    <button type="submit" class="btn btn-primary">Update dep</button>
                </form>
            </div>
        </div>
    </div>
</div>


@endforeach

</main>

<!-- Initialize DataTables -->
<script>
    $(document).ready(function () {
        $('#deps-table').DataTable();
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
</body>

</html>
