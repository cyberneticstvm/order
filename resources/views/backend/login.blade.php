<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Devi Eye Hospitals.">
  <meta name="keywords" content="Devi Eye Hospitals.">
  <meta name="author" content="Cybernetics">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <link rel="icon" href="{{ asset('/backend/assets/images/logo/devi-logo.png') }}" type="image/x-icon">
  <link rel="shortcut icon" href="{{ asset('/backend/assets/images/logo/devi-logo.png') }}" type="image/x-icon">
  <title>Devi Eye Hospitals - Login</title>
  <!-- Google font-->
  <link href="https://fonts.googleapis.com/css?family=Rubik:400,400i,500,500i,700,700i&amp;display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i,900&amp;display=swap" rel="stylesheet">
  <link rel="stylesheet" type="text/css" href="{{ asset('/backend/assets/css/font-awesome.css') }}">
  <!-- ico-font-->
  <link rel="stylesheet" type="text/css" href="{{ asset('/backend/assets/css/vendors/icofont.css') }}">
  <!-- Themify icon-->
  <link rel="stylesheet" type="text/css" href="{{ asset('/backend/assets/css/vendors/themify.css') }}">
  <!-- Flag icon-->
  <link rel="stylesheet" type="text/css" href="{{ asset('/backend/assets/css/vendors/flag-icon.css') }}">
  <!-- Feather icon-->
  <link rel="stylesheet" type="text/css" href="{{ asset('/backend/assets/css/vendors/feather-icon.css') }}">
  <!-- Plugins css start-->
  <!-- Plugins css Ends-->
  <!-- Bootstrap css-->
  <link rel="stylesheet" type="text/css" href="{{ asset('/backend/assets/css/vendors/bootstrap.css') }}">
  <!-- App css-->
  <link rel="stylesheet" type="text/css" href="{{ asset('/backend/assets/css/style.css') }}">
  <link id="color" rel="stylesheet" href="{{ asset('/backend/assets/css/color-1.css') }}" media="screen">
  <!-- Responsive css-->
  <link rel="stylesheet" type="text/css" href="{{ asset('/backend/assets/css/responsive.css') }}">
</head>

<body>
  <!-- login page start-->
  <div class="container-fluid p-0">
    <div class="row m-0">
      <div class="col-12 p-0">
        <div class="login-card login-dark">
          <div>
            <div><a class="logo" href="/login"><img class="img-fluid" src="{{ asset('/backend/assets/images/logo/devi-logo-devi.png') }}" alt=""></a></div>
            <div class="login-main">
              <form class="theme-form" method="post" action="{{ route('login') }}">
                @csrf
                <h4>Sign in to account</h4>
                <p>Enter your username & password to login</p>
                <div class="form-group">
                  <label class="col-form-label">Username</label>
                  {{ html()->text($name="username", $value=old('username'))->class('form-control')->placeholder("Username") }}
                  @error('username')
                  <small class="text-danger">{{ $errors->first('username') }}</small>
                  @enderror
                </div>
                <div class="form-group">
                  <label class="col-form-label">Password</label>
                  <div class="form-input position-relative">
                    {{ html()->password($name="password", $value=NULL)->class('form-control')->placeholder("******") }}
                    <div class="show-hide"><span class="show"></span></div>
                  </div>
                  @error('password')
                  <small class="text-danger">{{ $errors->first('password') }}</small>
                  @enderror
                </div>
                <div class="form-group mb-0">
                  <div class="checkbox p-0">
                    <input id="checkbox1" type="checkbox" name="remember" value="1">
                    <label class="text-muted" for="checkbox1">Remember password</label>
                  </div>
                  <div class="text-end mt-3">
                    <button class="btn btn-secondary btn-submit btn-block w-100" type="submit">Sign in</button>
                  </div>
                </div>
                <h6 class="text-center text-muted mt-4">Or Sign in with OTP</h6>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- latest jquery-->
    <script src="{{ asset('/backend/assets/js/jquery.min.js') }}"></script>
    <!-- Bootstrap js-->
    <script src="{{ asset('/backend/assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <!-- feather icon js-->
    <script src="{{ asset('/backend/assets/js/icons/feather-icon/feather.min.js') }}"></script>
    <script src="{{ asset('/backend/assets/js/icons/feather-icon/feather-icon.js') }}"></script>
    <!-- scrollbar js-->
    <!-- Sidebar jquery-->
    <script src="{{ asset('/backend/assets/js/config.js') }}"></script>
    <!-- Plugins JS start-->
    <!-- Plugins JS Ends-->
    <!-- Theme js-->
    <script src="{{ asset('/backend/assets/js/script.js') }}"></script>
  </div>
  @include("backend.message")
</body>

</html>