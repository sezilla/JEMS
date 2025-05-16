<!DOCTYPE html>
<html>
   <head>
      <!-- basic -->
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- mobile metas -->
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <meta name="viewport" content="initial-scale=1, maximum-scale=1">
      <!-- site metas -->
      <title>JEMS</title>
      <meta name="keywords" content="">
      <meta name="description" content="">
      <meta name="author" content="">
      <!-- bootstrap css -->
      <link rel="stylesheet" type="text/css" href="css/landingpage/bootstrap.min.css">
      <!-- style css -->
      <link rel="stylesheet" type="text/css" href="css/landingpage/style.css">
      <!-- Fullscreen css -->
      <link rel="stylesheet" type="text/css" href="css/landingpage/fullscreen.css">
      <!-- Responsive-->
      <link rel="stylesheet" href="css/landingpage/responsive.css">
      <!-- fevicon -->
      <link rel="icon" href="images/fevicon.png" type="image/gif" />
      <!-- font css -->
      <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700&display=swap" rel="stylesheet">
      <!-- Scrollbar Custom CSS -->
      <link rel="stylesheet" href="css/landingpage/jquery.mCustomScrollbar.min.css">
      <!-- Tweaks for older IEs-->
      <link rel="stylesheet" href="https://netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">
      <!-- Google Fonts - Poppins -->
      <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

   </head>
   <body>

   <div class="header_section">
   <div class="container">
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
         <a class="navbar-brand" href="index.html"><img src="images/logo.webp" style="height: 80px; width: auto;"></a>
         <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
         </button>
         <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ml-auto">
               <li class="nav-item active">
                  <a class="nav-link" href="{{ url('/') }}">Home</a>
               </li>
               <li class="nav-item">
                  <a class="nav-link" href="#about">About</a>
               </li>
               <li class="nav-item">
                  <a class="nav-link" href="#services">Services</a>
               </li>
            </ul>
            <form class="form-inline my-2 my-lg-0 mobile-login">
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

<style>
@media (max-width: 991px) {
    .mobile-login {
        display: flex;
        justify-content: center;
        width: 100%;
        margin-top: 10px;
        margin-bottom: 10px;
    }
    
    .mobile-login .dropdown {
        width: auto;
    }
    
    .mobile-login .btn {
        width: 100%;
        min-width: 120px;
    }
    
    .navbar-collapse {
        text-align: center;
    }
    
    .navbar-nav {
        margin-bottom: 0;
    }

    /* Add footer logo mobile styles */
    .footer-logo {
        text-align: center;
        margin-left: auto;
        margin-right: auto;
    }
}

/* Add styles for dropdown in full screen */
.dropdown {
    position: relative;
}

.dropdown-menu {
    position: absolute;
    right: 0;
    left: auto;
    min-width: 160px;
    z-index: 1000;
    display: none;
    float: left;
    padding: 0.5rem 0;
    margin: 0.125rem 0 0;
    font-size: 1rem;
    color: #212529;
    text-align: left;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid rgba(0,0,0,.15);
    border-radius: 0.25rem;
}

.dropdown-menu.show {
    display: block;
}

.form-inline {
    position: relative;
}

/* Ensure dropdown items are visible and properly styled */
.dropdown-item {
    display: block;
    width: 100%;
    padding: 0.25rem 1.5rem;
    clear: both;
    font-weight: 400;
    color: #212529;
    text-align: inherit;
    white-space: nowrap;
    background-color: transparent;
    border: 0;
}

.dropdown-item:hover, .dropdown-item:focus {
    color: #16181b;
    text-decoration: none;
    background-color: #f8f9fa;
}

/* Updated Footer logo styles */
.footer-logo {
    text-align: left;
}

.footer-logo img {
    width: auto;
    height: auto;
    display: block;
    transform: scale(2.3); /* Increase size without stretching */
    transform-origin: left center; /* Keep the scaling from the left side */
}

@media (max-width: 991px) {
    .footer-logo {
        text-align: center;
    }
    
    .footer-logo img {
        margin: 0 auto;
        transform: scale(1.5); /* Slightly smaller scale for mobile */
        transform-origin: center center; /* Scale from center for mobile */
    }
}

/* Fix for page scroll on reload */
html {
    scroll-behavior: smooth;
}

body {
    overflow-x: hidden;
}

/* Responsive font sizes for banner_taital */
@media (max-width: 1200px) {
    .banner_taital {
        font-size: 42px !important;
    }
}

@media (max-width: 992px) {
    .banner_taital {
        font-size: 36px !important;
    }
}

@media (max-width: 768px) {
    .banner_taital {
        font-size: 32px !important;
    }
}

@media (max-width: 576px) {
    .banner_taital {
        font-size: 28px !important;
    }
}

@media (max-width: 400px) {
    .banner_taital {
        font-size: 24px !important;
    }
}
</style>

      <!-- <div class="header_section">
         <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
               <a class="navbar-brand"href="index.html"><img src="images/logo.webp" style="height: 80px; width: auto;"></a>
               <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
               </button>
               <div class="collapse navbar-collapse" id="navbarSupportedContent">
                  <ul class="navbar-nav ml-auto">
                     <li class="nav-item active">
                        <a class="nav-link" href="index.html">Home</a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" href="about.html">About</a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" href="services.html">Services</a>
                     </li>
                  </ul>
                  <form class="form-inline my-2 my-lg-0">
                     <div class="login_bt"><a href="#">Login <span style="color: #222222;"><i class="fa fa-user" aria-hidden="true"></i></span></a></div>
                     <div class="fa fa-search form-control-feedback"></div>
                  </form>
                  <!-- <div class="absolute top-0 right-0 p-4 z-50" x-data="{ open: false }">
                     <div class="relative login_bt">
                        <a href="#" @click.prevent="open = !open" class="flex items-center gap-1 text-black dark:text-white">
                              Login 
                              <span style="color: #222222;"><i class="fa fa-user" aria-hidden="true"></i></span>
                        </a>

                        <div x-show="open" @click.away="open = false" x-transition
                              class="absolute right-0 mt-2 w-48 rounded-md bg-white shadow-lg ring-1 ring-black/10 dark:bg-gray-800">
                              
                              @auth
                                 <a href="{{ url('/app') }}"
                                    class="block px-4 py-2 text-sm text-black hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    Dashboard
                                 </a>
                                 <a href="{{ url('/admin/login') }}"
                                    class="block px-4 py-2 text-sm text-black hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    Admin Dashboard
                                 </a>
                              @else
                                 <a href="{{ url('app/login') }}"
                                    class="block px-4 py-2 text-sm text-black hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    Log in
                                 </a>
                                 <a href="{{ url('/admin/login') }}"
                                    class="block px-4 py-2 text-sm text-black hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                                    Log in as Admin
                                 </a>
                              @endauth
                        </div>
                     </div>
                  </div> -->

<!-- 
               </div>
            </nav>
         </div> -->
         <!-- banner section start --> 
         <div class="banner_section layout_padding">
            <div class="container">
               <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
                  <ol class="carousel-indicators">
                     <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active">01</li>
                     <li data-target="#carouselExampleIndicators" data-slide-to="1">02</li>
                     <li data-target="#carouselExampleIndicators" data-slide-to="2">03</li>
                     <li data-target="#carouselExampleIndicators" data-slide-to="3">04</li>
                     <li data-target="#carouselExampleIndicators" data-slide-to="4">05</li>
                     <li data-target="#carouselExampleIndicators" data-slide-to="5">06</li>
                  </ol>
                  <div class="carousel-inner">
                     <div class="carousel-item active">
                            <div class="row">
                             <div class="col-sm-6">
                              <h1 class="banner_taital">Coordination</h1>
                              <p class="banner_text">Oversees the overall planning and execution of events, ensuring all departments are aligned and tasks are completed on schedule. Acts as the central point of communication between clients and internal teams.</p>
                              <!-- <div class="started_text"><a href="#">Order Now</a></div> -->
                           </div>

                           <div class="col-sm-6">
                              <div class="banner_img"><img src="images/banner-img.png"></div>
                            </div>
                           
                        </div> 
                    
                        
                     </div>
                     <div class="carousel-item">
                        <div class="row">
                           <div class="col-sm-6">
                              <h1 class="banner_taital">Photo&Video</h1>
                              <p class="banner_text">Captures memorable moments through professional photography and videography. Manages editing, equipment, and ensures high-quality visual content delivery for every event.</p>
                              <!-- <div class="started_text"><a href="#">Order Now</a></div> -->
                           </div>
                           <div class="col-sm-6">
                              <div class="banner_img"><img src="images/banner-img2.png"></div>
                           </div>
                        </div>
                     </div>
                     <div class="carousel-item">
                        <div class="row">
                           <div class="col-sm-6">
                              <h1 class="banner_taital">Entertainment</h1>
                              <p class="banner_text">Provides engaging entertainment such as hosts, DJs, live performers, and other talents. Ensures that the entertainment aspect complements the theme and vibe of the event.</p>
                              <!-- <div class="started_text"><a href="#">Order Now</a></div> -->
                           </div>
                           <div class="col-sm-6">
                              <div class="banner_img"><img src="images/banner-img3.png"></div>
                           </div>
                        </div>
                     </div>
                     <div class="carousel-item">
                        <div class="row">
                           <div class="col-sm-6">
                              <h1 class="banner_taital">Catering</h1>
                              <p class="banner_text">Plans and prepares food and beverages tailored to the client's preferences. Ensures quality, presentation, and timely service of meals during events.</p>
                              <!-- <div class="started_text"><a href="#">Order Now</a></div> -->
                           </div>
                           <div class="col-sm-6">
                              <div class="banner_img"><img src="images/banner-img4.png"></div>
                           </div>
                        </div>
                     </div>
                     <div class="carousel-item">
                        <div class="row">
                           <div class="col-sm-6">
                              <h1 class="banner_taital">Floristry & Designing</h1>
                              <p class="banner_text">Responsible for creating beautiful event aesthetics through floral arrangements, decorations, and overall venue styling based on the chosen theme or concept.</p>
                              <!-- <div class="started_text"><a href="#">Order Now</a></div> -->
                           </div>
                           <div class="col-sm-6">
                              <div class="banner_img"><img src="images/banner-img5.png"></div>
                           </div>
                        </div>
                     </div>
                     <div class="carousel-item">
                        <div class="row">
                           <div class="col-sm-6">
                              <h1 class="banner_taital">HMUA</h1>
                              <p class="banner_text">Hair and Make Up Artists. Ensures clients and participants look their best by providing professional hair and makeup services. Coordinates with the event timeline for pre-event preparations.</p>
                              <!-- <div class="started_text"><a href="#">Order Now</a></div> -->
                           </div>
                           <div class="col-sm-6">
                              <div class="banner_img"><img src="images/banner-img.png"></div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <!-- banner section end -->
      </div>
      <!-- header section end -->
      <!-- about sectuion start -->
      <!-- <div class="about_section layout_padding">
         <div class="container">
            <div class="row">
               <div class="col-md-6">
                  <div class="about_img"><img src="images/about-img.png"></div>
               </div>
               <div class="col-md-6">
                  <h1 class="about_taital">About JEM</h1>
                  <p class="about_text">Jhossa Event Management, founded by Jhossa and Raymond, began its journey when Jhossa, while pursuing her Mass Communication degree, delved into event organization, particularly specializing in wedding events. Raymond, her husband, played a pivotal role in promoting Jhossa's passion and together they crafted affordable wedding packages. Launched in 2016, these packages quickly gained popularity, fueling the company's rapid growth. The demand for their services led to the expansion of their team, enabling them to efficiently handle numerous wedding events. Our company streamlines the wedding planning process by offering comprehensive packages, sparing our clients the hassle of dealing with multiple suppliers. Not only does this save them valuable time, but it also helps them save money compared to sourcing individual services from various vendors. Our packages are meticulously curated to encompass most, if not all, of the necessities for the future bride and groom. As of 2024, Jhossa Event Management has flourished, boasting a team of nearly 400 employees and earning widespread recognition in the event industry for our commitment to delivering high-quality wedding services. Our reputation for excellence has become synonymous with unforgettable wedding experiences. Continuing our journey, we remain steadfast in our pursuit of excellence, aiming to ascend to the forefront of the event management industry. Our dedication to crafting exceptional wedding events drives us forward as we strive to set new standards of excellence and distinction in our field.</p>
                  <div class="read_bt_1"><a href="#">Read More</a></div>
               </div>
            </div>
         </div>
      </div> -->
      <div class="about_section layout_padding" id="about">
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

      <!-- about sectuion end -->
      <!-- cream sectuion start -->
      <!-- <div class="cream_section layout_padding">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <h1 class="cream_taital">Our Featured Ice Cream</h1>
                  <p class="cream_text">tempor incididunt ut labore et dolore magna aliqua</p>
               </div>
            </div>
            <div class="cream_section_2">
               <div class="row">
                  <div class="col-md-4">
                     <div class="cream_box">
                        <div class="cream_img"><img src="images/img-1.png"></div>
                        <div class="price_text">$10</div>
                        <h6 class="strawberry_text">Strawberry Ice Cream</h6>
                        <div class="cart_bt"><a href="#">Add To Cart</a></div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="cream_box">
                        <div class="cream_img"><img src="images/img-2.png"></div>
                        <div class="price_text">$10</div>
                        <h6 class="strawberry_text">Strawberry Ice Cream</h6>
                        <div class="cart_bt"><a href="#">Add To Cart</a></div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="cream_box">
                        <div class="cream_img"><img src="images/img-1.png"></div>
                        <div class="price_text">$10</div>
                        <h6 class="strawberry_text">Strawberry Ice Cream</h6>
                        <div class="cart_bt"><a href="#">Add To Cart</a></div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="cream_section_2">
               <div class="row">
                  <div class="col-md-4">
                     <div class="cream_box">
                        <div class="cream_img"><img src="images/img-3.png"></div>
                        <div class="price_text">$10</div>
                        <h6 class="strawberry_text">Strawberry Ice Cream</h6>
                        <div class="cart_bt"><a href="#">Add To Cart</a></div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="cream_box">
                        <div class="cream_img"><img src="images/img-4.png"></div>
                        <div class="price_text">$10</div>
                        <h6 class="strawberry_text">Strawberry Ice Cream</h6>
                        <div class="cart_bt"><a href="#">Add To Cart</a></div>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="cream_box">
                        <div class="cream_img"><img src="images/img-5.png"></div>
                        <div class="price_text">$10</div>
                        <h6 class="strawberry_text">Strawberry Ice Cream</h6>
                        <div class="cart_bt"><a href="#">Add To Cart</a></div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="seemore_bt"><a href="#">See More</a></div>
         </div>
      </div> -->
      <!-- cream sectuion end -->
      <!-- services section start -->
      <div class="services_section layout_padding" id="services">
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
            <div class="seemore_bt"><a href="#">Back to Top</a></div>
         </div>
      </div>
      <!-- services section end -->
      <!-- testimonial section start -->
      <div class="testimonial_section layout_padding">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <h1 class="testimonial_taital">JEM MANAGEMENT</h1>
               </div>
            </div>
            <div class="testimonial_section_2">
               <div class="row">
                  <div class="col-md-12">
                     <div class="testimonial_box">
                        <div id="main_slider" class="carousel slide" data-ride="carousel">
                           <div class="carousel-inner">
                              <div class="carousel-item active">
                                 <h2>Chief Executive Officer</h2>
                                 <p class="testimonial_text">As a CEO of JEM, we have typical roles and general responsibilities to each of everyone especially to our clients. We manage our company and supervise employees and promote their growth. Review, approve plans and promote company as well.</p>
                                 <h4 class="client_name"><b>Jhossa</b> Dela Peña</h4>
                                 <div class="client_img"><img src="images/madam.webp" style="width: 155px; height: auto; object-fit: cover;"></div>
                              </div>
                              <div class="carousel-item">
                                 <h2>Chief Marketing Officer</h2>
                                 <p class="testimonial_text">Marketing used to be about making a myth and telling it. Now it's about telling a truth and share it. Jhossa Event Management moves forward by setting goals and then working to attain them. Bringing everyone together in pursuit of common goals is crucial to moving ahead, sustaining and growing this management over the long haul.</p>
                                 <h4 class="client_name"><b>Raymond</b> Dela Peña</h4>
                                 <div class="client_img"><img src="images/sir.webp" style="width: 145px; height: auto; object-fit: cover;"></div>
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
      <!-- contact section start -->
      <div class="contact_section layout_padding">
         <div class="container">
            <div class="row">
               <div class="col-md-4">
                  <div class="contact_main">
                     <div class="footer-logo">
                        <img src="images/logo.webp" alt="JEM Logo" class="footer-logo">
                     </div>
                  </div>
               </div>
               <div class="col-md-8">
                  <div class="location_text">
                     <ul>
                        <li>
                           <a href="#">
                           <span class="padding_left_10 active"><i class="fa fa-map-marker" aria-hidden="true"></i></span>3rd Flr Blk 1 Lt 11 LBV. Commercial Bldg, Molino Blvd, Bacoor, Cavite</a>
                        </li>
                        <li>
                           <a href="#">
                           <span class="padding_left_10"><i class="fa fa-phone" aria-hidden="true"></i></span> 0960-203-6297 | 0931-713-2954 | 0916-527-7174
                           </a>
                        </li>
                        <li>
                           <a href="mailto:jhossaeventmanagement@gmail.com">
                           <span class="padding_left_10"><i class="fa fa-envelope" aria-hidden="true"></i></span>jhossaeventmanagement@gmail.com
                           </a>
                        </li>
                     </ul>
                  </div>
                  <!-- <div class="mail_main">
                     <h3 class="newsletter_text">Newsletter</h3>
                     <div class="form-group">
                        <textarea class="update_mail" placeholder="Enter Your Email" rows="5" id="comment" name="Enter Your Email"></textarea>
                        <div class="subscribe_bt"><a href="#">Subscribe</a></div>
                     </div>
                  </div> -->
                  <div class="footer_social_icon">
                     <ul>
                        <li><a href="https://www.facebook.com/JhossaEM/" target="_blank" rel="noopener noreferrer"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                        <li><a href="https://www.instagram.com/jhossaeventmanagementig" target="_blank" rel="noopener noreferrer"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                        <li><a href="https://www.youtube.com/@jhossaeventmanagement7719" target="_blank" rel="noopener noreferrer"><i class="fa fa-youtube" aria-hidden="true"></i></a></li>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <!-- contact section end -->
      <!-- copyright section start -->
      <div class="copyright_section">
         <div class="container">
            <p class="copyright_text">2025 All Right Reserved By DDDM</p>
         </div>
      </div>
      <!-- copyright section end -->
      <!-- Javascript files-->
      <script src="js/landingpage/jquery.min.js"></script>
      <script src="js/landingpage/popper.min.js"></script>
      <script src="js/landingpage/bootstrap.bundle.min.js"></script>
      <script src="js/landingpage/jquery-3.0.0.min.js"></script>
      <script src="js/landingpage/plugin.js"></script>
      <!-- sidebar -->
      <script src="js/landingpage/jquery.mCustomScrollbar.concat.min.js"></script>
      <script src="js/landingpage/custom.js"></script>

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

      <!-- javascript --> 
   </body>
</html>