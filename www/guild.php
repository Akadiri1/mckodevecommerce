<?php
if(isset($_GET['message'])){
  $message = $_GET['message'];
}else{
  $message = "Good";
}
 ?>
 <!DOCTYPE html>
<html lang="en">


<!-- Mirrored from html.codedthemes.com/flash-able/bootstrap/default/maint-offline-ui.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 28 May 2019 23:39:12 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>

    <title>Flash Able - Most Trusted Admin Template</title>
    <!-- HTML5 Shim and Respond.js IE11 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 11]>
    	<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    	<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    	<![endif]-->
    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="description" content="Flash Able Bootstrap admin template made using Bootstrap 4 and it has huge amount of ready made feature, UI components, pages which completely fulfills any dashboard needs." />
    <meta name="keywords" content="admin templates, bootstrap admin templates, bootstrap 4, dashboard, dashboard templets, sass admin templets, html admin templates, responsive, bootstrap admin templates free download,premium bootstrap admin templates, Flash Able, Flash Able bootstrap admin template">
    <meta name="author" content="Codedthemes" />

    <!-- Favicon icon -->
    <link rel="icon" href="/da/assets/images/favicon.ico" type="image/x-icon">
    <!-- fontawesome icon -->
    <link rel="stylesheet" href="/da/assets/fonts/fontawesome/css/fontawesome-all.min.css">
    <!-- animation css -->
    <link rel="stylesheet" href="/da/assets/plugins/animation/css/animate.min.css">

    <!-- vendor css -->
    <link rel="stylesheet" href="/da/assets/css/style.css">




</head>

<!-- [ offline-ui ] start -->
<div class="auth-wrapper offline">
    <div class="text-center">
        <h1 class="mb-4">Hello!</h1>
        <h5 class="text-muted mb-4"><?php echo ucwords($message) ?></h5>
        <?php if (isset($_SERVER['HTTP_REFERER'])): ?>

            <a class="btn btn-primary mb-4 text-white" href="<?php echo $_SERVER['HTTP_REFERER'] ?>"><i class="feather icon-home"></i>Back</a>
          <?php else: ?>
             <a class="btn btn-primary mb-4 text-white" href="/"><i class="feather icon-home"></i>Back</a>
        <?php endif; ?>
    </div>
</div>
<!-- [ offline-ui ] end -->

<!-- Required Js -->
<script src="/da/assets/js/vendor-all.min.js"></script>
<script src="/da/assets/plugins/bootstrap/js/bootstrap.min.js"></script>




</body>


<!-- Mirrored from html.codedthemes.com/flash-able/bootstrap/default/maint-offline-ui.html by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 28 May 2019 23:39:12 GMT -->
</html>
