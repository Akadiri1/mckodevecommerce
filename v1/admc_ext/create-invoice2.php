<?php
$pageTitle = "Create Invoice";
// ob_start();
// session_start();
// $map = true;
include 'include/ext_auth.php';
include 'include/ext_header.php';


// define("DBNAME", "mckodevc_demo");
//  define("DBUSER", "root");
//  define("DBPASS", getenv('DB_PASSWORD'));

        try{

          $conn = new PDO('mysql:host=localhost;dbname='.DBNAME, DBUSER, DBPASS);

          $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        }
        catch(PDOException $e) {
                // die("Something Went Wrong: Configure a database with mckodevc_demo ");
                echo $e->getMessage();
        }


$fetchServices = selectContent($conn, "selection_service", ['visibility' => 'show']);

$service = [];

foreach ($fetchServices as $key => $value) {
  $pricing = selectContent($conn,"panel_pricing",["select_service" => $value['id']]);
  foreach ($pricing as $key2 => $value2) {
    $service[]['name'] = $value['input_title'].' - '.$value2['input_title'];
    // code...
  }
}

// die(var_dump($service));

$nigeria=["Abia", "Abuja FCT", "Adamawa", "Akwa Ibom", "Anambra", "Bauchi", "Bayelsa", "Benue", "Borno", "Cross River", "Delta", "Ebonyi", "Edo", "Ekiti", "Enugu", "Gombe", "Imo", "Jigawa", "Kaduna", "Kano", "Katsina", "Kebbi", "Kogi", "Kwara", "Lagos", "Nassarawa", "Niger", "Ogun", "Ondo", "Osun", "Oyo", "Plateau", "Rivers", "Sokoto", "Taraba", "Yobe", "Zamfara"];

 ?>

     <!-- fonts -->
     <link rel="preconnect" href="https://fonts.googleapis.com/">

     <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin>

     <link
         href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,wght@0,400;0,500;0,700;1,400;1,500;1,700&amp;display=swap"
         rel="stylesheet">
     <link href="https://fonts.googleapis.com/css2?family=Amatic+SC:wght@400;700&amp;display=swap" rel="stylesheet">
     <link rel="stylesheet" href="/da/assets/plugins/select2/css/select2.min.css">
     <link rel="stylesheet" href="/assets/vendors/bootstrap/css/bootstrap.min.css" />
     <link rel="stylesheet" href="/assets/vendors/animate/animate.min.css" />
     <link rel="stylesheet" href="/assets/vendors/animate/custom-animate.css" />
     <link rel="stylesheet" href="/assets/vendors/fontawesome/css/all.min.css" />
     <link rel="stylesheet" href="/assets/vendors/jarallax/jarallax.css" />
     <link rel="stylesheet" href="/assets/vendors/jquery-magnific-popup/jquery.magnific-popup.css" />
     <link rel="stylesheet" href="/assets/vendors/nouislider/nouislider.min.css" />
     <link rel="stylesheet" href="/assets/vendors/nouislider/nouislider.pips.css" />
     <link rel="stylesheet" href="/assets/vendors/odometer/odometer.min.css" />
     <link rel="stylesheet" href="/assets/vendors/swiper/swiper.min.css" />
     <link rel="stylesheet" href="/assets/vendors/agrion-icons/style.css">
     <link rel="stylesheet" href="/assets/vendors/tiny-slider/tiny-slider.min.css" />
     <link rel="stylesheet" href="/assets/vendors/reey-font/stylesheet.css" />
     <link rel="stylesheet" href="/assets/vendors/owl-carousel/owl.carousel.min.css" />
     <link rel="stylesheet" href="/assets/vendors/owl-carousel/owl.theme.default.min.css" />
     <link rel="stylesheet" href="/assets/vendors/bxslider/jquery.bxslider.css" />
     <link rel="stylesheet" href="/assets/vendors/bootstrap-select/css/bootstrap-select.min.css" />
     <link rel="stylesheet" href="/assets/vendors/vegas/vegas.min.css" />
     <link rel="stylesheet" href="/assets/vendors/jquery-ui/jquery-ui.css" />
     <link rel="stylesheet" href="/assets/vendors/timepicker/timePicker.css" />
     <link rel="stylesheet" href="assets/vendors/nice-select/nice-select.css" />

     <!-- template styles -->
     <link rel="stylesheet" href="/assets/css/agrion.css" />
     <link rel="stylesheet" href="/assets/css/agrion-responsive.css" />
     <style media="screen">
     @media (max-width: 991px) {
       .container{
         padding-left: 12px !important;
       }
       .main-header-two__logo {
         left: -126px;
       }
     }

     @media only screen and (min-width: 768px) and (max-width: 990px){
       .main-menu-two__wrapper-inner{
         margin-left: 131px;
       }
       .main-header-two__logo {
         left: -106px;
       }
     }


     @media (max-width: 767px) {
       .main-header-two__logo{
         left: -80px !important;
       }
       .main-menu-two__wrapper-inner {
           background-color: transparent !important;
       }
       .main-menu .mobile-nav__toggler{
         color: #fff !important;
       }
     }
     </style>

 </head>

 <body class="custom-cursor">

     <div class="custom-cursor__cursor"></div>
     <div class="custom-cursor__cursor-two"></div>


     <div class="preloader">
         <div class="preloader__image"></div>
     </div>
     <!-- /.preloader -->

        <!--Start Checkout Page-->
        <section class="checkout-page" style="margin-top:100px">
            <div class="container">
                <div class="row">
                    <div class="col-xl-12 col-lg-6">
                        <div class="billing_details">
                            <div class="billing_title">
                                <!-- <p>Returning Customer? <span><a href="/login">Click here to Login</a></span></p> -->
                                <h2>Fill the form to Create an Invoice for a User</h2>
                            </div>
                            <form class="billing_details_form" method="post" id="submit">
                                <?php if(isset($error['firstname'])){
                                echo "<p style='color:red'>".$error['firstname']."</p>";
                                } ?>
                                <div class="row bs-gutter-x-20">
                                    <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <input type="text" name="name" value="" placeholder="Name"
                                                required="">
                                        </div>
                                    </div>
                                      <?php if(isset($error['lastname'])){
                                      echo "<p style='color:red'>".$error['lastname']."</p>";
                                      } ?>
                                    <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <input type="text" name="email" value="" placeholder="Email Address"
                                                required="">
                                        </div>
                                    </div>

                                      <?php if(isset($error['phone'])){
                                      echo "<p style='color:red'>".$error['email']."</p>";
                                      } ?>
                                    <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <input type="tel" name="phone"
                                                required="" placeholder="Phone Number">
                                        </div>
                                    </div>

                                    <?php if(isset($error['email'])){
                                    echo "<p style='color:red'>".$error['email']."</p>";
                                    } ?>
                                  <div class="col-xl-6">
                                    <div class="billing_input_box">
                                        <div class="select-box">
                                            <select class="wide nice-select"  name="title" style="display:block">
                                              <option data-display="Select a country">--Select a Service--</option>
                                              <?php foreach ($service as $key => $value): ?>
                                                <option value="<?=$value['name']?>"> <?=$value['name']?></option>
                                              <?php endforeach; ?>
                                            </select>

                                        </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="row">
                                  <?php if(isset($error['pword'])){
                                  echo "<p style='color:red'>".$error['pword']."</p>";
                                  } ?>
                                  <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <input type="password" name="pword" value="" placeholder="Password">
                                        </div>
                                    </div>
                                      <?php if(isset($error['cpword'])){
                                      echo "<p style='color:red'>".$error['cpword']."</p>";
                                      } ?>
                                    <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <input type="password" name="cpword" value="" placeholder="Confirm Password">
                                        </div>
                                    </div>
                                      <?php if(isset($error['company'])){
                                      echo "<p style='color:red'>".$error['company']."</p>";
                                      } ?>
                                    <div class="col-xl-6">
                                            <div class="billing_input_box">
                                                <input type="text" name="company" value="" placeholder="Company">
                                            </div>
                                        </div>
                                          <?php if(isset($error['address'])){
                                          echo "<p style='color:red'>".$error['address']."</p>";
                                          } ?>
                                    <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <input type="text" name="address" value="" placeholder="Address">
                                        </div>
                                    </div>

                                </div>
                                <div class="row bs-gutter-x-20">
                                    <?php if(isset($error['state'])){
                                    echo "<p style='color:red'>".$error['state']."</p>";
                                    } ?>
                                      <div class="col-xl-6">
                                        <div class="billing_input_box">
                                            <div class="select-box">
                                                <select class="wide nice-select"  name="state" style="display:block">
                                                  <option data-display="Select a country">Select a Country</option>
                                                  <?php foreach ($nigeria as $key => $value): ?>
                                                    <option value="<?=$value?>"><?=$value?></option>
                                                  <?php endforeach; ?>
                                                </select>

                                            </div>
                                        </div>
                                    </div>
                                        <?php if(isset($error['city'])){
                                        echo "<p style='color:red'>".$error['city']."</p>";
                                        } ?>
                                  <div class="col-xl-6">
                                      <div class="billing_input_box">
                                          <input type="text" name="city" value="" placeholder="Town / City"
                                              required="">
                                      </div>
                                  </div>
                                </div>
                            </form>
                        </div>
                    </div>



                </div>

                <div class="project-page-one__btn">
                        <!-- <input type="submit" name="submit" form="submit" class="thm-btn project-page__btn" value="Create Account"> -->
                        <button type="submit" name="submit" form="submit" class="thm-btn project-page__btn" >Create Account <i class="icon-right-arrow"></i></button>
                        <!-- <a name="submit" form="submit" class="thm-btn project-page__btn">Create Account <i class="icon-right-arrow"></i></a> -->
                    </div>
        </section>
        <!--End Checkout Page-->
        <script src="/da/assets/plugins/select2/js/select2.full.min.js"></script>
        <script src="/da/assets/js/pages/form-select-custom.js"></script>
        <script src="/assets/vendors/jquery/jquery-3.6.0.min.js"></script>
        <script src="/assets/vendors/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="/assets/vendors/jarallax/jarallax.min.js"></script>
        <script src="/assets/vendors/jquery-ajaxchimp/jquery.ajaxchimp.min.js"></script>
        <script src="/assets/vendors/jquery-appear/jquery.appear.min.js"></script>
        <script src="/assets/vendors/jquery-circle-progress/jquery.circle-progress.min.js"></script>
        <script src="/assets/vendors/jquery-magnific-popup/jquery.magnific-popup.min.js"></script>
        <script src="/assets/vendors/jquery-validate/jquery.validate.min.js"></script>
        <script src="/assets/vendors/nouislider/nouislider.min.js"></script>
        <script src="/assets/vendors/odometer/odometer.min.js"></script>
        <script src="/assets/vendors/swiper/swiper.min.js"></script>
        <script src="/assets/vendors/tiny-slider/tiny-slider.min.js"></script>
        <script src="/assets/vendors/wnumb/wNumb.min.js"></script>
        <script src="/assets/vendors/wow/wow.js"></script>
        <script src="/assets/vendors/isotope/isotope.js"></script>
        <script src="/assets/vendors/countdown/countdown.min.js"></script>
        <script src="/assets/vendors/owl-carousel/owl.carousel.min.js"></script>
        <script src="/assets/vendors/bxslider/jquery.bxslider.min.js"></script>
        <script src="/assets/vendors/bootstrap-select/js/bootstrap-select.min.js"></script>
        <script src="/assets/vendors/vegas/vegas.min.js"></script>
        <script src="/assets/vendors/jquery-ui/jquery-ui.js"></script>
        <script src="/assets/vendors/timepicker/timePicker.js"></script>
        <script src="/assets/vendors/circleType/jquery.circleType.js"></script>
        <script src="/assets/vendors/circleType/jquery.lettering.min.js"></script>
        <!-- <script src="assets/vendors/nice-select/jquery.nice-select.min.js"></script> -->
        <script src="/ajax/ajax.min.js"></script>



        <!-- template js -->
        <script src="/assets/js/agrion.js"></script>
        <script src="/assets/js/sweetalert2.all.min.js"></script>
        </body>


        </html>
