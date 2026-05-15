<?php
if(isset($_GET['message'])){
  $message = $_GET['message'];
}else{
  $message = "Something Went Wrong";
}
 ?>
 <!DOCTYPE html>
 <html lang="en">


 <!-- Mirrored from html.codedthemes.com/flash-able/bootstrap/default/maint-error.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 28 May 2019 23:38:39 GMT -->
 <!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
 <head>

     <title>Error!</title>

     <meta charset="utf-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
     <meta http-equiv="X-UA-Compatible" content="IE=edge" />
     <meta name="description" content="Error Page" />
     <meta name="keywords" content="Error Page">


     <!-- Favicon icon -->
     <link rel="icon" href="/dummy.png" type="image/x-icon">
     <!-- fontawesome icon -->
     <link rel="stylesheet" href="/da/assets/fonts/fontawesome/css/fontawesome-all.min.css">
     <!-- animation css -->
     <link rel="stylesheet" href="/da/assets/plugins/animation/css/animate.min.css">

     <!-- vendor css -->
     <link rel="stylesheet" href="/da/assets/css/style.css">




 </head>

 <!-- [ offline-ui ] start -->
 <div class="auth-wrapper error">
     <div id="container" class="container">
         <ul id="scene" class="scene">
           <h4 class="text-white">Error!</h4>
             <li class="layer" data-depth="1.00"><img class="img-fluid" width="50" height="50" src="/dummy.png" alt="images"> <br><br><br><br><br> <h5 class="text-white"><?php echo ucwords($message) ?></h5></li>


         </ul>
         <?php if (isset($_SERVER['HTTP_REFERER'])): ?>

             <a class="btn btn-outline-light mt-3 mb-4 text-white" href="<?php echo $_SERVER['HTTP_REFERER'] ?>"><i class="feather icon-home"></i>Back</a>

           <?php else: ?>
              <a class="btn btn-outline-light mt-3 mb-4" href="/"><i class="feather icon-home"></i>Back</a>

         <?php endif; ?>

     </div>
 </div>
 <!-- [ offline-ui ] end -->
 <script src="/da/assets/js/vendor-all.min.js"></script>
 <script src="/da/assets/plugins/bootstrap/js/bootstrap.min.js"></script>

 <script src="/da/assets/js/pages/error.js"></script>

 <script>
     var scene = document.getElementById('scene');
     var parallax = new Parallax(scene);
 </script>





 </body>


 </html>
