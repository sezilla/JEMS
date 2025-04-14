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
   </head>
   <body>
      <div class="header_section">
         <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light bg-light">
               <a class="navbar-brand"href="index.html"><img src="images/logo.webp" style="height: 80px; width: auto;"></a>
               <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
               <span class="navbar-toggler-icon"></span>
               </button>
               <div class="collapse navbar-collapse" id="navbarSupportedContent">
                  <!-- <ul class="navbar-nav ml-auto">
                     <li class="nav-item active">
                        <a class="nav-link" href="index.html">Home</a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" href="about.html">About</a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" href="icecream.html">Icecream</a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" href="services.html">Services</a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" href="blog.html">Blog</a>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" href="contact.html">Contact Us</a>
                     </li>
                  </ul> -->
                  <!-- <form class="form-inline my-2 my-lg-0">
                     <div class="login_bt"><a href="#">Login <span style="color: #222222;"><i class="fa fa-user" aria-hidden="true"></i></span></a></div>
                     <div class="fa fa-search form-control-feedback"></div>
                  </form> -->
                  <div class="absolute top-0 right-0 p-4 z-50" x-data="{ open: false }">
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
                  </div>


               </div>
            </nav>
         </div>
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
                              <p class="banner_text">Plans and prepares food and beverages tailored to the client’s preferences. Ensures quality, presentation, and timely service of meals during events.</p>
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
      <div class="about_section layout_padding">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <div class="about_img"><img src="images/about-img.jpg" alt="About JEM Image"></div>
                </div>
                <div class="col-md-6">
                    <h1 class="about_taital">About JEM</h1>
                    <p class="about_text" id="aboutText">
                    Jhossa Event Management, founded by Jhossa and Raymond, began its journey when Jhossa, while pursuing her Mass Communication degree, delved into event organization, particularly specializing in wedding events. Raymond, her husband, played a pivotal role in promoting Jhossa's passion and together they crafted affordable wedding packages. 
                    <span id="dots">...</span>
                    <span id="moreText" style="display: none;">
                    Their growing success led to the expansion of their team and services, allowing them to handle multiple weddings with efficiency and care.
                     The company stands out by offering comprehensive, all-in-one wedding packages that spare clients the stress of coordinating with multiple vendors. This not only saves time but also significantly reduces costs. Each package is thoughtfully curated to include everything a couple needs for their big day.
                     By 2024, Jhossa Event Management has grown to a team of nearly 400 and earned a strong reputation for delivering high-quality, memorable wedding experiences. Committed to excellence, the company continues to innovate and lead in the event industry, setting new standards with every celebration they create.</p>
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
      <div class="services_section layout_padding">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <h1 class="services_taital">Our Ice Cream Services</h1>
                  <p class="services_text">tempor incididunt ut labore et dolore magna aliqua</p>
               </div>
            </div>
            <div class="services_section_2">
               <div class="row">
                  <div class="col-md-4">
                     <div class="services_box">
                        <h5 class="tasty_text"><span class="icon_img"><img src="images/icon-1.png"></span>Cookies Ice Cream</h5>
                        <p class="lorem_text">commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fat </p>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="services_box">
                        <h5 class="tasty_text"><span class="icon_img"><img src="images/icon-2.png"></span>Cookies Ice Cream</h5>
                        <p class="lorem_text">commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fat </p>
                     </div>
                  </div>
                  <div class="col-md-4">
                     <div class="services_box">
                        <h5 class="tasty_text"><span class="icon_img"><img src="images/icon-1.png"></span>Cookies Ice Cream</h5>
                        <p class="lorem_text">commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fat </p>
                     </div>
                  </div>
               </div>
            </div>
            <div class="seemore_bt"><a href="#">Read More</a></div>
         </div>
      </div>
      <!-- services section end -->
      <!-- testimonial section start -->
      <div class="testimonial_section layout_padding">
         <div class="container">
            <div class="row">
               <div class="col-md-12">
                  <h1 class="testimonial_taital">Testimonial</h1>
               </div>
            </div>
            <div class="testimonial_section_2">
               <div class="row">
                  <div class="col-md-12">
                     <div class="testimonial_box">
                        <div id="main_slider" class="carousel slide" data-ride="carousel">
                           <div class="carousel-inner">
                              <div class="carousel-item active">
                                 <p class="testimonial_text">tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint</p>
                                 <h4 class="client_name">Marri Fen</h4>
                                 <div class="client_img"><img src="images/client-img.png"></div>
                              </div>
                              <div class="carousel-item">
                                 <p class="testimonial_text">tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint</p>
                                 <h4 class="client_name">Marri Fen</h4>
                                 <div class="client_img"><img src="images/client-img.png"></div>
                              </div>
                              <div class="carousel-item">
                                 <p class="testimonial_text">tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint</p>
                                 <h4 class="client_name">Marri Fen</h4>
                                 <div class="client_img"><img src="images/client-img.png"></div>
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
                     <h1 class="contact_taital">Contact Us</h1>
                     <form action="/action_page.php">
                        <div class="form-group">
                           <input type="text" class="email-bt" placeholder="Name" name="Name">
                        </div>
                        <div class="form-group">
                           <input type="text" class="email-bt" placeholder="Email" name="Name">
                        </div>
                        <div class="form-group">
                           <input type="text" class="email-bt" placeholder="Phone Numbar" name="Email">
                        </div>
                        <div class="form-group">
                           <textarea class="massage-bt" placeholder="Massage" rows="5" id="comment" name="Massage"></textarea>
                        </div>
                     </form>
                     <div class="main_bt"><a href="#">SEND</a></div>
                  </div>
               </div>
               <div class="col-md-8">
                  <div class="location_text">
                     <ul>
                        <li>
                           <a href="#">
                           <span class="padding_left_10 active"><i class="fa fa-map-marker" aria-hidden="true"></i></span>Making this the first true</a>
                        </li>
                        <li>
                           <a href="#">
                           <span class="padding_left_10"><i class="fa fa-phone" aria-hidden="true"></i></span>Call : +01 1234567890
                           </a>
                        </li>
                        <li>
                           <a href="#">
                           <span class="padding_left_10"><i class="fa fa-envelope" aria-hidden="true"></i></span>Email : demo@gmail.com
                           </a>
                        </li>
                     </ul>
                  </div>
                  <div class="mail_main">
                     <h3 class="newsletter_text">Newsletter</h3>
                     <div class="form-group">
                        <textarea class="update_mail" placeholder="Enter Your Email" rows="5" id="comment" name="Enter Your Email"></textarea>
                        <div class="subscribe_bt"><a href="#">Subscribe</a></div>
                     </div>
                  </div>
                  <div class="footer_social_icon">
                     <ul>
                        <li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                        <li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                        <li><a href="#"><i class="fa fa-linkedin" aria-hidden="true"></i></a></li>
                        <li><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
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
            <p class="copyright_text">2020 All Rights Reserved. Design by <a href="https://html.design">Free Html Templates</a> Distribution by <a href="https://themewagon.com">ThemeWagon</a></p>
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