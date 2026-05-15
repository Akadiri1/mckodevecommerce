<?php
$pageTitle = "Manage Invoice";
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


$fetchInvoice = selectContent($conn, "invoice", ['status' => 'Unpaid']);


?>
<!DOCTYPE html>
<html lang="en">

<head>



    	<meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    	<meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- fontawesome icon -->
    	<link rel="stylesheet" href="/da/assets/fonts/fontawesome/css/fontawesome-all.min.css">
    	<!-- animation css -->
    	<link rel="stylesheet" href="/da/assets/plugins/animation/css/animate.min.css">


          <link rel="stylesheet" type="text/css" href="da/assets/css/font-awesome-n.min.css">
          <link rel="stylesheet" type="text/css" href="da/assets/css/font-awesome.min.css">
          <!-- select2 css -->
          <link rel="stylesheet" href="/da/assets/plugins/select2/css/select2.min.css">
          <!-- multi-select css -->
          <link rel="stylesheet" href="/da/assets/plugins/multi-select/css/multi-select.css">
    	<!-- notification css -->
    	<link rel="stylesheet" href="/da/assets/plugins/notification/css/notification.min.css">

          <!-- data tables css -->
          <link rel="stylesheet" href="/da/assets/plugins/data-tables/css/datatables.min.css">

    	<!-- vendor css -->
	<!-- <link rel="stylesheet" type="text/css" href="da/assets/sweetalert/sweetalert2.min.css"> -->
    	<link rel="stylesheet" href="/da/assets/css/styles.css">
    <!-- <script type="text/javascript" src="/ckeditor/ckeditor.js"></script> -->
         <!-- <script src="/ckfinder/ckfinder.js"></script> -->




    </head>
<body>
<script src="/ajax/ajax.js"></script>
<section class="container">
    <div class="wrapper">
        <div class="content">
            <div class="inner-content">
                <div class="main-body">
                    <div class="page-wrapper">
                        <!-- [ breadcrumb ] start -->
                        <!-- <div class="page-header bg-primary">
                            <div class="page-block">
                                <div class="row align-items-center">
                                    <div class="col-md-12">
                                        <div class="page-header-title">
                                            <h5 class="m-b-10">Orders</h5>
                                        </div>
                                        <ul class="breadcrumb">
                                            <li class="breadcrumb-item"><a href="/adminhome"><i class="fa fa-home"></i></a></li>
                                            <li class="breadcrumb-item"><a href="#!">Projects</a></li>
                                            <li class="breadcrumb-item"><a href="#!">Manage Projects</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div> -->
                        <!-- Button trigger modal -->

                        <!-- [ breadcrumb ] end -->
                        <!-- [ Main Content ] start -->
                        <div class="row">
                            <!-- HTML5 Export Buttons table start -->
                            <div class="col-sm-12">
                                <div class="card">
                                    <div class="card-header table-card-header">
                                        <h5>Unpaid Invoices</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="dt-responsive table-responsive">
                                            <table id="new-cons" class="table table-striped table-bordered nowrap">

                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Service</th>
                                                        <th>Amount</th>
                                                        <th>Payment Method</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                  <tbody>
                                                  <?php foreach ($fetchInvoice as $key => $value): ?>


                                                    <tr>
                                                        <td><?php echo $value['name'] ?></td>
                                                        <td><?php echo $value['email'] ?></td>
                                                        <td><?php echo $value['title'] ?></td>
                                                        <td><?php echo $value['amount_due'] ?></td>
                                                        <td><?php echo $value['payment_method'] ?></td>
                                                        <td>
                                                            <a href="#" onclick="updateInvoice('<?= $value['hash_id'] ?>')" class="btn btn-success">Approve</a><br>

                                                            <!-- <a href="#" onclick="updateInvoice('<?//= $value['hash_id'] ?>')" class="btn btn-danger">Reject</a> -->
                                                        </td>
                                                    </tr>


                                                    <?php endforeach; ?>
                                                  </tbody>
                                                <tfoot>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Email</th>
                                                        <th>Service</th>
                                                        <th>Amount</th>
                                                        <th>Payment Method</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- [ Main Content ] end -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>




    <!-- Required Js -->
    <script src="/da/assets/js/vendor-all.min.js"></script>
    <script src="/da/assets/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="/da/assets/js/pcoded.min.js"></script>


  <!-- select2 Js -->
  <script src="/da/assets/plugins/select2/js/select2.full.min.js"></script>

  <!-- multi-select Js -->
  <script src="/da/assets/plugins/multi-select/js/jquery.quicksearch.js"></script>
  <script src="/da/assets/plugins/multi-select/js/jquery.multi-select.js"></script>

  <!-- form-select-custom Js -->
  <script src="/da/assets/js/pages/form-select-custom.js"></script>
  <!-- file-upload Js -->
  <script src="/da/assets/plugins/fileupload/js/dropzone-amd-module.min.js"></script>

  <!-- datatable Js -->
  <script src="/da/assets/plugins/data-tables/js/datatables.min.js"></script>
  <script src="/da/assets/plugins/data-tables/js/buttons.colVis.min.js"></script>
  <script src="/da/assets/js/pages/data-export-custom.js"></script>


  <script src="/da/assets/js/pages/data-responsive-custom.js"></script>


<!-- dashboard-custom js -->
<script src="/da/assets/js/pages/dashboard-analytics.js"></script>
<script src="/da/assets/plugins/sweetalert/js/sweetalert2.all.min.js"></script>

<script type="text/javascript">
// var sweet_loader = '<img src="/loading-gif.gif">';

    function updateInvoice(id){
  swal.fire({
          title: "Are you sure?",
          text: "This invoice will be set as paid and value will be given to the user.",
          icon: "question",
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, Update!'
      })
      .then((result) => {
          if (result.value) {

                var param = {
                    'id' : id,
                    'status' : 'paid',
                    // 'message' : result.value
                }

                // swal.fire({
                // html: '<h4>Loading...</h4>',
                // showConfirmButton: false,
                // onRender: function() {
                // $('.swal2-content').prepend(sweet_loader);
                //     }
                //
                // });
            ajaxPost("/update", param, function(err, response){
            // console.log(response);
            var res = JSON.parse(response);
            if(res.success){
              swal.fire("Success!", "Invoice Status has been updated!.", "success");
              window.location.reload();
            }
            // if(res.failed){
            //     swal.fire("Failed!", "Message was not sent. Try again after some time.", "error");
            // }
            if(res.error){
              swal.fire("Error!","Error Occured ", "error");
            }
          });


      //
          } else {
              swal.fire("Cancelled!"," ", "error");
          }
        // }
      });

}



</script>

</body>

</html>
