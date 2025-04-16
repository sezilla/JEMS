<!DOCTYPE html>
<html>
   <head>
      <!-- basic -->
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- site metas -->
      <title>About</title>
      <meta name="keywords" content="">
      <meta name="description" content="">
      <meta name="author" content="">

      <!-- Styles -->
      <link rel="stylesheet" type="text/css" href="{{ asset('css/landingpage/bootstrap.min.css') }}">
      <link rel="stylesheet" type="text/css" href="{{ asset('css/landingpage/style.css') }}">
      <link rel="stylesheet" href="{{ asset('css/landingpage/responsive.css') }}">
      <link rel="icon" href="{{ asset('images/fevicon.png') }}" type="image/gif" />
      <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="{{ asset('css/landingpage/jquery.mCustomScrollbar.min.css') }}">
      <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
      <!-- Google Fonts - Poppins -->
      <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
   </head>
   <body>
      <div class="header_section header_bg">
         <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
               <a class="navbar-brand" href="index.html"><img src="images/logo.webp" style="height: 80px; width: auto;"></a>
               <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                  <span class="navbar-toggler-icon"></span>
               </button>
               <div class="collapse navbar-collapse" id="navbarSupportedContent">
                  <ul class="navbar-nav ml-auto">
                     <li class="nav-item {{ request()->is('/') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('/') }}">Home</a>
                     </li>
                     <li class="nav-item {{ request()->is('about') ? 'active' : '' }}">
                        <a class="nav-link" href="{{ url('about') }}">About</a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" href="{{ url('services') }}">Services</a>
                     </li>
                  </ul>
                  <form class="form-inline my-2 my-lg-0">
                     <!-- Dropdown for Login and Admin Login -->
                     <div class="dropdown">
                        <button class="btn dropdown-toggle" type="button" id="loginDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                           <span style="color: #222222;">Login <i class="fa fa-user" aria-hidden="true"></i></span>
                        </button>
                        <div class="dropdown-menu" aria-labelledby="loginDropdown">
                           <a class="dropdown-item" href="app/login">Log in</a>
                           <a class="dropdown-item" href="admin/login">Log in as Admin</a>
                        </div>
                     </div>
                  </form>
               </div>
            </nav>
         </div>
      </div>

      <!-- about section start -->
      <div class="about_section layout_padding">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="about_img"><img src="images/about-img.jpg" alt="About JEM Image"></div>
                </div>
                <div class="col-md-6">
                    <h1 class="about_taital">About JEM</h1>
                    <p class="about_text" id="aboutText">
                    Jhossa Event Management, founded by Jhossa and Raymond, began with a focus on affordable wedding packages. 
                    <span id="dots">...</span>
                    <span id="moreText" style="display: none;">
                     What started as a passion project during Jhossa's Mass Communication studies grew into a thriving business, offering all-in-one wedding solutions. By 2024, the company expanded to a team of nearly 400, known for delivering high-quality, memorable weddings while saving clients time and money. Committed to excellence, Jhossa Event Management continues to set new industry standards.</p>
                    <div class="read_bt_1">
                    <a href="javascript:void(0);" onclick="toggleReadMore()" id="readMoreBtn">Read More</a>
                    </div>
                </div>
            </div>
        </div>
      </div>
      <!-- about section end -->

      <!-- copyright section start -->
      <div class="copyright_section margin_top90">
         <div class="container">
            <p class="copyright_text">2025 All Right Reserved By DDDM</p>
            </p>
         </div>
      </div>
      <!-- copyright section end -->

      <!-- Scripts -->
      <script src="{{ asset('js/landingpage/jquery.min.js') }}"></script>
      <script src="{{ asset('js/landingpage/popper.min.js') }}"></script>
      <script src="{{ asset('js/landingpage/bootstrap.bundle.min.js') }}"></script>
      <script src="{{ asset('js/landingpage/jquery-3.0.0.min.js') }}"></script>
      <script src="{{ asset('js/landingpage/plugin.js') }}"></script>
      <script src="{{ asset('js/landingpage/jquery.mCustomScrollbar.concat.min.js') }}"></script>
      <script src="{{ asset('js/landingpage/custom.js') }}"></script>

      <script>
        function toggleReadMore() {
        const dots = document.getElementById("dots");
        const moreText = document.getElementById("moreText");
        const btnText = document.getElementById("readMoreBtn");

        if (dots.style.display === "none") {
            dots.style.display = "inline";
            moreText.style.display = "none";
            btnText.textContent = "Read More";
        } else {
            dots.style.display = "none";
            moreText.style.display = "inline";
            btnText.textContent = "Read Less";
        }
        }
      </script>
   </body>
</html>
