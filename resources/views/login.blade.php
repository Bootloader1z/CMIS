<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Login </title>
  <meta content="" name="description">
  <meta content="" name="keywords">
  <!-- Favicons -->
  <link href="{{ asset('assets/img/logo.png') }}" rel="icon">
  <link href="{{ asset('assets/img/logo.png') }}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/quill/quill.snow.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/quill/quill.bubble.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
  <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">

  <style>
    .toast-success {
        background-color: #28a745 !important; /* Green background */
        color: white; /* White text */
    }
</style>

  <!-- Template Main CSS File -->
  <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Updated: Apr 20 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-6 col-md-10 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="{{route('landpage')}}" class="logo d-flex align-items-center w-auto">
                  <img src="{{asset('assets/img/logo.png')}}" alt="">
                  <span class="d-none d-lg-block">LAND TRANSPORTATION OFFICE</span>
                </a>
              </div><!-- End Logo -->
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


              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Traffic Adjudication Service </h5>
                    <p class="text-center small">Case Management System</p>
                  </div>

                  <form id="loginForm" class="row g-3 needs-validation" novalidate method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    <div class="col-12">
                        <label for="yourUsername" class="form-label">Username</label>
                        <div class="input-group has-validation">
                            <span class="input-group-text" id="inputGroupPrepend">@</span>
                            <input type="text" name="username" class="form-control" id="yourUsername" required>
                            <div class="invalid-feedback">Please enter your username.</div>
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="yourPassword" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="yourPassword" required>
                        <div class="invalid-feedback">Please enter your password!</div>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" value="true" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Remember me</label>
                        </div>
                        <input type="hidden" name="remember" value="false">
                    </div>

                    <div class="col-12">
                        <button id="loginButton" class="btn btn-primary w-100" type="submit">Login</button>
                    </div>
                </form>

                </div>
              </div>

              <div class="credits">
                <!-- All the links in the footer should remain intact. -->
                <!-- You can delete the links only if you purchased the pro version. -->
                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->

              </div>

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->
  <script src="{{ asset('assets/js/main.js') }}"></script>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
  <script>
    $(document).ready(function() {
        // Prevent form submission on click if validation fails
        $('#loginForm').on('submit', function(e) {
            e.preventDefault(); // Prevent the default form submission

            // Check for form validity
            if (!this.checkValidity()) {
                // If form is invalid, show feedback and stop
                e.stopPropagation();
                this.classList.add('was-validated');
                return;
            }

            let form = $(this); // Reference to the form
            let formData = form.serialize(); // Serialize form data for submission

            // Disable the button and show a loading message
            disableButton();

            toastr.options = {
                "positionClass": "toast-top-center",  // Position the toast at the top center
                "timeOut": "3000", // Optional: Set how long the toastr will stay visible (in milliseconds)
                "closeButton": true, // Optional: Add a close button to the toastr
                "progressBar": true, // Optional: Show a progress bar while the toastr is visible
            };

            $.ajax({
                type: form.attr('method'),  // Use the method of the form
                url: form.attr('action'),   // Use the action URL of the form
                data: formData,
            })
            .done(function(data) {
                if (data.success) {
                    toastr.success(data.message);

                    setTimeout(function() {
                        window.location.href = data.redirect;  // Redirect after success
                    }, 1500);
                } else {
                    toastr.error(data.message);  // Show error message if not successful
                }
            })
            .fail(function(xhr) {
                // Handle errors returned from the server
                if (xhr.status === 401) {
                    toastr.error('Invalid username or password.');
                } else {
                    toastr.error('An error occurred during login. Please try again.');
                }
            })
            .always(function() {
                // Enable the button back if needed (in case of error)
                $('#loginButton').prop('disabled', false).text('Login');
            });
        });
    });

    function disableButton() {
        var button = document.getElementById('loginButton');
        button.disabled = true;
        button.innerHTML = 'Logging in...';
    }
</script>




  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
<!-- Vendor JS Files -->
<script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/vendor/chart.js/chart.umd.js') }}"></script>
<script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
<script src="{{ asset('assets/vendor/quill/quill.js') }}"></script>
<script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
<script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
<script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>

<!-- Template Main JS File -->


</body>

</html>
