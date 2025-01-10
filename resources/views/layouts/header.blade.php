<header id="header" class="header fixed-top d-flex align-items-center">
  <!-- Loading Screen -->
  <div id="pageLoader" class="page-loader">
    <img src="{{asset('assets/img/logo.png')}}" alt="">
    <div class="spinner-border" role="status">
      <span class="visually-hidden">Loading...</span>
    </div>
    <strong>Loading...</strong>

</div>

    <div class="d-flex align-items-center justify-content-between">
      <a href="{{url('/')}}" class="logo d-flex align-items-center">
      <img src="{{asset('assets/img/logo.png')}}" alt="">
                  <span class="d-none d-lg-block">Traffic Adjudication Service </span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
        <script>
          var myVar=setInterval(function(){myTimer()},1000);
          function myTimer() {
              var d = new Date();
              document.getElementById("horas").innerHTML = d.toLocaleTimeString();
          }
          </script>

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <a><i class="bi bi-clock-fill"></i>
          Time&ensp;: <span class="badge badge-primary"style="background-color: white; color: black;" id="horas">NULL</span>
          </a>

       <!-- Notifications Dropdown -->
<li class="nav-item dropdown">
    <a class="nav-link nav-icon" href="#" id="notificationsDropdown" data-bs-toggle="dropdown">
        <i class="bi bi-bell"></i>
        <span  id="notificationCount" class="badge bg-primary badge-number">0</span> <!-- Update this dynamically -->
    </a>
    <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
        <li class="dropdown-header">
            Notifications
            <a href="#"><span class="badge rounded-pill bg-primary p-2 ms-2">View all</span></a>
        </li>
        <li>
            <hr class="dropdown-divider">
        </li>
        <!-- Notification Items will be populated dynamically -->
    </ul>
</li>
<!-- End Notifications Dropdown -->

<script>
    // Function to fetch notifications and update dropdown
    function fetchNotifications() {
        $.ajax({
            url: "{{ route('notifications') }}",
            type: 'GET',
            success: function(response) {
                $('#notificationCount').text(response.length);
                $('.notifications .dropdown-header span').text(response.length);
                $('.notifications .notification-item').remove(); // Clear existing items

                // Populate notifications dropdown
                response.forEach(function(notification) {
                    var iconClass = getIconClass(notification.type); // Function to get icon class based on notification type

                    var createdAt = moment(notification.created_at).format('YYYY-MM-DD HH:mm:ss');

                    var html = '<li class="notification-item">';
                    html += '<i class="' + iconClass + '"></i>';
                    html += '<div>';
                    html += '<h4>' + notification.model + '</h4>';
                    html += '<p>' + notification.description + '</p>';
                    html += '<p style="display:none;">' + notification.details + '</p>';
                    html += '<p>By: ' + notification.user.fullname + '</p>';
                    html += '<p>' + createdAt + '</p>'; // Adjust timestamp as per your needs
                    html += '<p style="display:none;">' + notification.old_value + '</p>';
                    html += '<p style="display:none;">' + notification.new_value + '</p>';
                    html += '</div>';
                    html += '</li>';

                    $('.notifications').append(html);
                });
            },
            error: function(error) {
                console.error('Error fetching notifications:', error);
            }
        });
    }

    // Function to get icon class based on notification type
    function getIconClass(type) {
        switch (type) {
            case 'warning':
                return 'bi-exclamation-circle text-warning';
            case 'danger':
                return 'bi-x-circle text-danger';
            case 'success':
                return 'bi-check-circle text-success';
            case 'primary':
                return 'bi-info-circle text-primary';
            default:
                return 'bi-info-circle text-primary';
        }
    }

    $(document).ready(function() {
    fetchNotifications(); // Fetch notifications on page load

    // Open modal and populate details on notification item click
    $(document).on('click', '.notification-item', function() {
        var title = $(this).find('h4').text().trim();
        var description = $(this).find('p:nth-of-type(1)').text().trim(); // Adjust selector for description
        var fullname = $(this).find('p:nth-of-type(3)').text().trim(); // Adjust selector for fullname
        var timestamp = $(this).find('p:nth-of-type(4)').text().trim(); // Adjust selector for timestamp
        var old_value = $(this).find('p:nth-of-type(5)').text().trim(); // Adjust selector for old_value
        var new_value = $(this).find('p:nth-of-type(6)').text().trim(); // Adjust selector for new_value

        var detailsText = $(this).find('p:nth-of-type(2)').text().trim();
        var details;

        try {
            details = JSON.parse(detailsText); // Parse details as JSON object
        } catch (error) {
            console.error('Error parsing details:', error);
            details = {}; // Default to empty object if parsing fails
        }

        // Create table for details
        var tableHtml = '<table class="table table-bordered">';
        tableHtml += '<tbody>';
        tableHtml += '<tr><td>Title</td><td>' + title + '</td></tr>';
        tableHtml += '<tr><td>Description</td><td>' + description + '</td></tr>';
        tableHtml += '<tr><td>User</td><td>' + fullname + '</td></tr>';
        tableHtml += '<tr><td>Timestamp</td><td>' + timestamp + '</td></tr>';

        if (details && typeof details === 'object' && Object.keys(details).length > 0) {
            // Display details as an ordered list
            tableHtml += '<tr><td>Details</td><td><ul>';
            Object.keys(details).forEach(function(key) {
                tableHtml += '<li><strong>' + key + ':</strong> ' + details[key] + '</li>';
            });
            tableHtml += '</ul></td></tr>';
        }

        tableHtml += '<tr><td>Old Value</td><td>' + old_value + '</td></tr>';
        tableHtml += '<tr><td>New Value</td><td>' + new_value + '</td></tr>';
        tableHtml += '</tbody>';
        tableHtml += '</table>';

        // Populate modal content
        $('#notificationDetails').html(tableHtml);

        // Show the modal
        $('#notificationModal').modal('show');
    });
});


</script>


        <li class="nav-item dropdown pe-4">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            @if(Auth::user()->profile_pic)
                <img src="{{ asset(Auth::user()->profile_pic) }}" alt="User's Profile Picture" class="nav-link nav-profile d-flex align-items-center pe-0" style="width: 50px; height: 50px; border-radius: 50%;">
            @else
                <img src="{{ asset('assets/img/pzpx.png') }}" alt="Default User Image" style="width: 50px; height: auto; border-radius: 50%;">
            @endif
            <span class="d-none d-md-block dropdown-toggle ps-2">{{Auth::user()->fullname}}</span>
          </a><!-- End Profile Iamge Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              @if(Auth::user()->profile_pic)
                <img src="{{ asset(Auth::user()->profile_pic) }}" alt="User's Profile Picture" class="profile-pic" style="width: 100px; height: 100px;">
              @else
                  <img src="{{ asset('assets/img/pzpx.png') }}" alt="Default User Image" style="width: 100px; height: auto; border-radius: 50%;">
              @endif
              <h6>{{Auth::user()->fullname}}</h6>
              @if (Auth::user()->role == 9)
                  <span>Administrator</span>
              @else
                  <span>Employee</span>
              @endif
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{ route('profile', ['id' => Auth::id()]) }}">
                <i class="bi bi-person"></i>
                <span>My Profile</span>
              </a>
            </li>
            @if (Auth::user()->role == 9)
            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{route('user_management')}}">
                <i class="bi bi-person-fill-add"></i>
                <span>User Management</span>
              </a>
            </li>
            @endif
            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{route('logout')}}">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->
