

@section('title', env('APP_NAME'))

@include('layouts.title')

<body>

  <!-- ======= Header ======= -->
@include('layouts.header')

  <!-- ======= Sidebar ======= -->
 @include('layouts.sidebar')

  <main id="main" class="main">

  

    <section class="section dashboard">
      <div class="row">

        <div class="card">
            <div class="filter">
                <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                        <h6>Filter</h6>
                    </li>
                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                </ul>
            </div>
        
            <div class="card-body">
                <h5 class="card-title">Recent Activity <span>| Today</span></h5>
        
                <div class="activity">
                    @foreach ($activities as $activity)
                        <div class="activity-item d-flex">
                            <div class="activite-label">{{ \Carbon\Carbon::parse($activity['timestamp'])->diffForHumans() }}</div>
                            <i class="bi bi-circle-fill activity-badge text-success align-self-start"></i>
                            <div class="activity-content">
                                {{ $activity['fullname'] }} {{ $activity['action'] }} 
                                @if(isset($activity['changes']) && is_array($activity['changes']))
                                    @foreach ($activity['changes'] as $field => $value)
                                        <strong>{{ $field }}</strong>: {{ $value }} 
                                    @endforeach
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        

      </div>
    </section>

  </main><!-- End #main -->

 @include('layouts.footer')
</body>

</html>