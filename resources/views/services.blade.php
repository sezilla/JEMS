<!DOCTYPE html>
<html lang="en">
<head>
   <!-- basic -->
   <meta charset="utf-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Services</title>
   <!-- CSS files -->
   <link rel="stylesheet" href="{{ asset('css/landingpage/bootstrap.min.css') }}">
   <link rel="stylesheet" href="{{ asset('css/landingpage/style.css') }}">
   <link rel="stylesheet" href="{{ asset('css/landingpage/responsive.css') }}">
   <link rel="stylesheet" href="{{ asset('css/landingpage/jquery.mCustomScrollbar.min.css') }}">
   <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
   <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
   <link rel="icon" href="{{ asset('images/fevicon.png') }}" type="image/gif">
   <!-- Google Fonts - Poppins -->
   <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
   <div class="header_section header_bg">
      <div class="container">
         <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="{{ url('/') }}">
               <img src="{{ asset('images/logo.webp') }}" style="height: 80px; width: auto;">
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
               aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
               <ul class="navbar-nav ml-auto">
                  <li class="nav-item {{ request()->is('/') ? 'active' : '' }}">
                     <a class="nav-link" href="{{ url('/') }}">Home</a>
                  </li>
                  <li class="nav-item {{ request()->is('about') ? 'active' : '' }}">
                     <a class="nav-link" href="{{ url('/about') }}">About</a>
                  </li>
                  <li class="nav-item {{ request()->is('services') ? 'active' : '' }}">
                     <a class="nav-link" href="{{ url('/services') }}">Services</a>
                  </li>
               </ul>
               <form class="form-inline my-2 my-lg-0">
                  <div class="dropdown">
                     <button class="btn dropdown-toggle" type="button" id="loginDropdown" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                        <span style="color: #222222;">Login <i class="fa fa-user" aria-hidden="true"></i></span>
                     </button>
                     <div class="dropdown-menu" aria-labelledby="loginDropdown">
                        <a class="dropdown-item" href="{{ url('app/login') }}">Log in</a>
                        <a class="dropdown-item" href="{{ url('admin/login') }}">Log in as Admin</a>
                     </div>
                  </div>
               </form>
            </div>
         </nav>
      </div>
   </div>

   <!-- services section start -->
   <div class="services_section layout_padding">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <h1 class="services_taital"><b>One of the best</b>
                  <br>Event Management</h1>
                  <p class="services_text">Jhossa Event Management offer Best Deal Affordable one stop shop wedding packages for stress and hassle free wedding event.</p>
               </div>
            </div>
            <div class="services_section_2">
               <div class="row">
                  <div class="col-md-4">
                     <div class="services_box">
                        <h5 class="tasty_text"><span class="icon_img"><img src="images/003-support.png"></span>Friendly Team</h5>
                        <p class="lorem_text">Our friendly team ensures smooth, stress-free planning.</p>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="services_box">
                        <h5 class="tasty_text"><span class="icon_img"><img src="images/002-balloons.png"></span>Perfect Venues</h5>
                        <p class="lorem_text">Choose from stunning venues that match your style, size, and budget.</p>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="services_box">
                        <h5 class="tasty_text"><span class="icon_img"><img src="images/004-cheers.png"></span>Full Coordination</h5>
                        <p class="lorem_text">We handle every detail so you can relax and enjoy your event.</p>
                     </div>
                  </div>
               </div>
            <div class="services_section_2">
               <div class="row">
                     <div class="col-md-4">
                        <div class="services_box">
                           <h5 class="tasty_text"><span class="icon_img"><img src="images/006-camera.png"></span>Timeless Memories</h5>
                           <p class="lorem_text">We bring your vision to life with lasting, joyful memories.</p>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="services_box">
                           <h5 class="tasty_text"><span class="icon_img"><img src="images/001-live-chat.png"></span>24/7 Support</h5>
                           <p class="lorem_text">Need help anytime? We're here around the clock for you.</p>
                        </div>
                     </div>
                     <div class="col-md-4">
                        <div class="services_box">
                           <h5 class="tasty_text"><span class="icon_img"><img src="images/007-lightbulb.png"></span>Brilliant Ideas</h5>
                           <p class="lorem_text">Fresh, creative ideas to make your event truly stand out.</p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <!-- <div class="seemore_bt"><a href="#">Back to Top</a></div> -->
         </div>
      </div>
      <!-- services section end -->
      <!-- testimonial section start -->
      <div class="testimonial_section layout_padding">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <h1 class="testimonial_taital">THIS IS JEM!</h1>
               </div>
            </div>
            <div class="testimonial_section_2 d-flex justify-content-center">
               <div class="row">
                  <div class="col-md-12">
                     <div class="testimonial_box p-0 border-0" style="max-width: 800px; margin: auto;">
                        <div id="main_slider" class="carousel slide" data-ride="carousel">
                           <div class="carousel-inner">
                              <div class="carousel-item active">
                                 <div class="client_img">
                                    <img src="{{ asset('images/jem1.jpg') }}"  alt="JEM 1">
                                 </div>
                              </div>
                              <div class="carousel-item">
                                 <div class="client_img">
                                    <img src="{{ asset('images/jem2.jpg') }}"  alt="JEM 2">
                                 </div>
                              </div>
                              <div class="carousel-item">
                                 <div class="client_img">
                                    <img src="{{ asset('images/jem3.jpg') }}" alt="JEM 3">
                                 </div>
                              </div>
                              <div class="carousel-item">
                                 <div class="client_img">
                                    <img src="{{ asset('images/jem4.jpg') }}" alt="JEM 4">
                                 </div>
                              </div>
                              <div class="carousel-item">
                                 <div class="client_img">
                                    <img src="{{ asset('images/jem5.jpg') }}" alt="JEM 5">
                                 </div>
                              </div>
                           </div>
                           <a class="carousel-control-prev" href="#main_slider" role="button" data-slide="prev">
                              <i class="fa fa-angle-left"></i>
                           </a>
                           <a class="carousel-control-next" href="#main_slider" role="button" data-slide="next">
                              <i class="fa fa-angle-right"></i>
                           </a>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>

      <!-- testimonial section end -->

   <!-- Footer -->
   <div class="copyright_section margin_top90">
      <div class="container">
         <p class="copyright_text">2025 All Right Reserved By DDDM</p>
         </p>
      </div>
   </div>

   <!-- Scripts -->
   <script src="{{ asset('js/landingpage/jquery.min.js') }}"></script>
   <script src="{{ asset('js/landingpage/popper.min.js') }}"></script>
   <script src="{{ asset('js/landingpage/bootstrap.bundle.min.js') }}"></script>
   <script src="{{ asset('js/landingpage/jquery-3.0.0.min.js') }}"></script>
   <script src="{{ asset('js/landingpage/plugin.js') }}"></script>
   <script src="{{ asset('js/landingpage/jquery.mCustomScrollbar.concat.min.js') }}"></script>
   <script src="{{ asset('js/landingpage/custom.js') }}"></script>
</body>
</html>
