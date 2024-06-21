<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="{{ settings()?->company_name }}">
  <meta name="keywords" content="{{ settings()?->company_name }}">
  <meta name="author" content="Cybernetics">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <link rel="icon" href="{{ asset('/backend/assets/images/logo/devi-logo.png') }}" type="image/x-icon">
  <link rel="shortcut icon" href="{{ asset('/backend/assets/images/logo/devi-logo.png') }}" type="image/x-icon">
  <title>{{ settings()?->company_name }} - Admin</title>
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
  <link rel="stylesheet" type="text/css" href="{{ asset('/backend/assets/css/vendors/slick.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('/backend/assets/css/vendors/slick-theme.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('/backend/assets/css/vendors/scrollbar.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('/backend/assets/css/vendors/animate.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('/backend/assets/css/vendors/datatables.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.6.0/bootstrap-tagsinput.min.css" rel="stylesheet" type="text/css" />
  <!-- Plugins css Ends-->
  <link rel="stylesheet" type="text/css" href="{{ asset('/backend/assets/css/vendors/drawer/bootstrap-drawer.css') }}">
  <!-- Bootstrap css-->
  <link rel="stylesheet" type="text/css" href="{{ asset('/backend/assets/css/vendors/bootstrap.css') }}">
  <!-- App css-->
  <link rel="stylesheet" type="text/css" href="{{ asset('/backend/assets/css/style.css') }}">
  <link id="color" rel="stylesheet" href="{{ asset('/backend/assets/css/color-6.css') }}" media="screen">
  <!-- Responsive css-->
  <link rel="stylesheet" type="text/css" href="{{ asset('/backend/assets/css/responsive.css') }}">
</head>

<body>
  <!-- loader starts-->
  <div class="loader-wrapper">
    <div class="loader-index"> <span></span></div>
    <svg>
      <defs></defs>
      <filter id="goo">
        <fegaussianblur in="SourceGraphic" stddeviation="11" result="blur"></fegaussianblur>
        <fecolormatrix in="blur" values="1 0 0 0 0  0 1 0 0 0  0 0 1 0 0  0 0 0 19 -9" result="goo"> </fecolormatrix>
      </filter>
    </svg>
  </div>
  <!-- loader ends-->
  <!-- tap on top starts-->
  <div class="tap-top"><i data-feather="chevrons-up"></i></div>
  <!-- tap on tap ends-->
  <!-- page-wrapper Start-->
  <div class="page-wrapper compact-wrapper" id="pageWrapper">
    <!-- Page Header Start-->
    <div class="page-header">
      <div class="header-wrapper row m-0">
        <div class="header-logo-wrapper col-auto p-0">
          <div class="logo-wrapper">
            <a href="{{ route('dashboard') }}"><img class="img-fluid" src="{{ asset('/backend/assets/images/logo/devi-logo.png') }}" alt=""></a>
          </div>
          <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="align-center"></i></div>
        </div>
        <div class="nav-right col-xxl-7 col-xl-6 col-md-7 col-8 pull-right right-header p-0 ms-auto">
          <ul class="nav-menus">
            @if(Session::has('branch'))
            <li class="language-nav">
              <div class="translate_wrapper">
                <div class="current_lang">
                  <div class="lang"><i class="fa fa-refresh" aria-hidden="true"></i><span class="lang-txt">{{ branches()->where('id', Session::get('branch'))->first()->name }}</span></div>
                </div>
                <div class="more_lang">
                  @forelse(userBranches()->where('id', '!=', Session::get('branch')) as $key => $branch)
                  <div class="lang" data-value="{{ $branch->name }}"><i class="fa fa-check-circle-o fa-lg" aria-hidden="true"></i><span class="lang-txt"><a href="{{ route('switch.branch', encrypt($branch->id)) }}" class="text-dark">{{ $branch->name }}</a></span></div>
                  @empty
                  @endforelse
                </div>
              </div>
            </li>
            @endif
            <li class="profile-nav onhover-dropdown pe-0 py-0">
              <div class="media profile-media"><img class="b-r-10" src="{{ asset('/backend/assets/images/dashboard/profile.png') }}" alt="">
                <div class="media-body"><span>{{ Auth::user()?->name }}</span>
                  <p class="mb-0">{{ Auth::user()?->roles->first()->name }}<i class="middle fa fa-angle-down"></i></p>
                </div>
              </div>
              <ul class="profile-dropdown onhover-show-div">
                <li><a href="{{ route('user.change.pwd') }}"><i data-feather="user"></i><span>Account</span></a></li>
                <li><a href="{{ route('logout') }}"><i data-feather="log-out"> </i><span>Log out</span></a></li>
              </ul>
            </li>
          </ul>
        </div>
      </div>
    </div>
    <!-- Page Header Ends                              -->
    <!-- Page Body Start-->
    <div class="page-body-wrapper">
      <!-- Page Sidebar Start-->
      <div class="sidebar-wrapper" sidebar-layout="stroke-svg">
        <div>
          <div class="logo-wrapper"><a href="{{ route('dashboard') }}"><img class="img-fluid for-light" src="{{ asset('/backend/assets/images/logo/devi-logo.png') }}" alt="" style="width: 50%;"><img class="img-fluid for-dark" src="{{ asset('/backend/assets/images/logo/devi-logo.png') }}" alt="" style="width: 50%;"></a>
            <div class="back-btn"><i class="fa fa-angle-left"></i></div>
            <div class="toggle-sidebar"><i class="status_toggle middle sidebar-toggle" data-feather="grid"> </i></div>
          </div>
          <div class="logo-icon-wrapper"><a href="{{ route('dashboard') }}">DEVI</a></div>
          <nav class="sidebar-main">
            <div class="left-arrow" id="left-arrow"><i data-feather="arrow-left"></i></div>
            @include("backend.nav")
            <div class="right-arrow" id="right-arrow"><i data-feather="arrow-right"></i></div>
          </nav>
        </div>
      </div>
      <!-- Page Sidebar Ends-->
      @yield("content")
      <!-- footer start-->
      <footer class="footer">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12 footer-copyright text-center">
              <p class="mb-0">Copyright {{ date('Y') }} © {{ ucwords(settings()->company_name) }}</p>
            </div>
          </div>
        </div>
      </footer>
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
  <script src="{{ asset('/backend/assets/js/scrollbar/simplebar.js') }}"></script>
  <script src="{{ asset('/backend/assets/js/scrollbar/custom.js') }}"></script>
  <!-- Sidebar jquery-->
  <script src="{{ asset('/backend/assets/js/config.js') }}"></script>
  <!-- Plugins JS start-->
  <script src="{{ asset('/backend/assets/js/drawer/source.js') }}"></script>
  <script src="{{ asset('/backend/assets/js/drawer/bootstrap-drawer.js') }}"></script>
  <script src="{{ asset('/backend/assets/js/sidebar-menu.js') }}"></script>
  <script src="{{ asset('/backend/assets/js/sidebar-pin.js') }}"></script>
  <script src="{{ asset('/backend/assets/js/slick/slick.min.js') }}"></script>
  <script src="{{ asset('/backend/assets/js/slick/slick.js') }}"></script>
  <script src="{{ asset('/backend/assets/js/header-slick.js') }}"></script>

  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js" type="text/javascript"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-tagsinput/0.6.0/bootstrap-tagsinput.min.js" type="text/javascript"></script>
  <script src="{{ asset('/backend/assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('/backend/assets/js/datatable/datatables/datatable.custom1.js') }}"></script>
  <script src="{{ asset('/backend/assets/js/datatable/datatable_advance.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <!-- Plugins JS Ends-->
  <!-- Theme js-->
  @if(Route::current()?->getName() == 'dashboard')
  <script src="{{ asset('/backend/assets/js/chart/apex-chart/apex-chart.js') }}"></script>
  <script src="{{ asset('/backend/assets/js/chart/apex-chart/stock-prices.js') }}"></script>
  <script src="{{ asset('/backend/assets/js/dashboard/dashboard.js') }}"></script>
  @endif
  <script src="{{ asset('/backend/assets/js/script.js') }}"></script>
  <script src="{{ asset('/backend/assets/js/devi.js') }}"></script>
  <script src="{{ asset('/backend/assets/js/theme-customizer/customizer.js') }}"></script>
  @include("backend.message")
</body>

</html>