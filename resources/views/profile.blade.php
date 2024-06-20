@include('layouts.title')

<body>
  <!-- Include Header -->
  @include('layouts.header')

  <!-- Include Sidebar -->
  @include('layouts.sidebar')
  <style>
   /* Global Styles */
body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  background-color: #f5f5f5;
}

.container-fluid {
  padding: 20px;
}

.card {
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  padding: 20px;
}

.card-title {
  font-size: 2em;
  font-weight: bold;
  margin-bottom: 20px;
}

.alert {
  padding: 0.75rem 1.25rem;
  margin-bottom: 1rem;
  border: 1px solid transparent;
  border-radius: 0.25rem;
}

/* Profile Picture Styling */
.profile-pic {
  width: 200px;
  height: 200px;
  border-radius: 50%;
  object-fit: cover;
  margin-bottom: 20px;
}

.profile-picture-form {
  margin-top: 20px;
}

/* Buttons */
.btn {
  margin-right: 10px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .profile-pic {
    width: 150px;
    height: 150px;
  }
}
  </style>
  <main id="main" class="main">
    <div class="container-fluid">
      <div class="row">
        <div class="col-md-12 h-100">
          <div class="card">
            <div class="card-body">
              @if ($errors->any())
                <div class="alert alert-danger">
                  <ul>
                    @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                    @endforeach
                  </ul>
                </div>
              @endif
              
              <h1 class="card-title">User Information</h1>
              @if($user->profile_pic)
                <img src="{{ asset($user->profile_pic) }}" alt="User's Profile Picture" class="profile-pic" style="width: 200px; height: 200px;">
              @else
                  <img src="{{ asset('assets/img/pzpx.png') }}" alt="Default User Image" style="width: 100px; height: auto; border-radius: 50%;">
              @endif
              
              <div class="mt-4">
                <form method="POST" action="{{ route('profile.picture.upload', ['id' => $user->id]) }}" enctype="multipart/form-data" class="profile-picture-form">
                    @csrf
                    <input type="file" name="profile_picture" id="profile_picture" accept="image/*">
                    <button type="submit" class="btn btn-primary">Upload Picture</button>
                </form>
            </div>

              <br>
              <p>Name: {{ $user->fullname }}</p>
              <p>Email: {{ $user->email }}</p>
                
              <a href="{{ route('profile.edit', ['id' => $user->id]) }}" class="btn btn-primary mt-8">Edit Profile</a>
              <a href="{{ route('profile.change', ['id' => $user->id]) }}" class="btn btn-success">Change Password</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Include Footer -->
  @include('layouts.footer')
</body>

</html>