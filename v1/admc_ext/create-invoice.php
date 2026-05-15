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


$fetchUsers = selectContent($conn, "read_members", []);
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


if (isset($_POST['submit'])) {

  // var_dump($_POST);
// die(var_dump($_POST));


  $error = [];

  $post = $_POST;

  unset($post['submit']);
  foreach($post as $key => $value){

    // skip if referral code is empty
    // if (in_array($key, ["referral",]) ) {
    // 	continue;
    // }

    if (empty($value)) {
      // code...
      $error[$key] = true;
    }
  }

  // var_dump($_POST, $error);
  if (empty($error)) {
       $hash_id = rand(0,99999).time();
       $num = rand(000000,999999).'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
       $refCode= substr(str_shuffle($num),0,9);


       $data['hash_id'] = $hash_id;
       $data['visibility'] = "show";
      $data['user_id'] = $details[0]['hash_id'];
       $data['input_phone_number'] = $post['phone_no'];
       $data['input_service'] = $post['service'];
       $data['input_amount'] = $post['amount'];
       $data['input_farm_location'] = $post['farm_location'];
       $data['input_farm_state'] = $post['state'];
       $data['input_farm_name'] = $post['farm_name'];
       $data['input_crop_product'] = $post['crop_product'];
       $data['input_number_of_labour'] = $post['number_of_labour'];
       $data['input_needed_labour_date'] = $post['needed_labour_date'];
       $data['date_created'] = date('Y-m-d');
       $data['time_created'] = date('H:i:s');

       // var_dump($data);
       // die;
        insertSafe($conn, "read_requests", $data);

    // $stmt = $conn->prepare("INSERT INTO `read_realtor`(`hash_id`, `visibility`, `input_first_name`, `input_last_name`,  `input_email`,`input_phone_number`, `dated_date_of_birth`, `input_gender`, `input_state`, `input_year_of_experience`, `input_whatsapp_number`, `input_location`, `input_bank_name`, `input_account_name`, `input_account_number`, `input_referral_code`, `input_referred_by`, `date_created`, `time_created`) VALUES ($hash_id, 'show', :fName, :lName, :email, :phone_no, :dob, :gender, :state, :year_of_experience, :whatsapp_number, :location, :bank_name, :account_name, :account_number,$hash_id,:referral, NOW(), NOW())");

      // if($execute){
        unset($_POST);
        header("location:/checkout?plan=$hash_id");
      // }
  }else{
     // $_SESSION['error'] = "Please fill the required fields";
    $errorText = "Please fill the required fields";
  }

// var_dump($_POST);

}

 ?>

 <div class="//pcoded-main-container">
     <div class="pcoded-wrapper">
         <div class="pcoded-content">
             <div class="pcoded-inner-content">
                 <div class="main-body">
                     <div class="page-wrapper">
                         <!-- [ breadcrumb ] start -->

                         <!-- [ breadcrumb ] end -->
                         <!-- [ Main Content ] start -->
                         <div class="row d-flex justify-content-center">
                           <div class="col-md-8">
                               <div class="card">
                                   <!-- <div class="card-header">
                                       <h5>Basic Componant</h5>
                                   </div> -->
                                   <div class="card-body">
                                     <h5>Fill in details below to Create Invoice</h5>
                                        <hr>
                                        <!-- <div class="row"> -->


                                          <!-- <div class="col-md-6"> -->
                                            <form action="" method="post">
                                              <?php if (isset($errorText)): ?>
                                                <p style="color:red"><?=$errorText?></p>
                                              <?php endif ?>
                                              <div class="form-row">
                                                <div class="form-group col-md-6">
                                                    <label for="">User <span class="text-danger">*</span></label>
                                                    <select class="js-example-basic-single form-control select2-hidden-accessible" name="member_id">
                                                      <option value="">--select User--</option>
                                                      <?php foreach ($fetchUsers as $key => $value): ?>
                                                        <option value="<?php echo $value['id'] ?>"><?php echo $value['input_firstname'].' '.$value['input_lastname'] ?></option>
                                                      <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <!-- <div class="form-group col-md-6" >
                                                    <label for="">Service <span class="text-danger">*</span></label>
                                                    <select class="js-example-basic-single form-control select2-hidden-accessible" name="title">
                                                      <option value="">--select Service--</option>
                                                      <option value="priced-service">Priced Service</option>
                                                      <option value="consultation">Consultation</option>
                                                    </select>
                                                </div> -->
                                                  <div class="form-group col-md-6" >
                                                      <label for="">Pricing Plan<span class="text-danger">*</span></label>
                                                      <select class="js-example-basic-single form-control select2-hidden-accessible" name="title">
                                                        <option value="">--select pricing plan--</option>
                                                        <option value="consultation">Consultation</option>
                                                        <?php foreach ($service as $key => $value): ?>
                                                          <option value="<?php echo $value['name'] ?>"><?php echo $value['name'] ?></option>
                                                        <?php endforeach; ?>
                                                      </select>
                                                  </div>


                                                <div class="form-group col-md-6">
                                                    <label>Amount Due <span class="text-danger">*</span></label>

                                                    <input type="number" id="amount" class="form-control" name="input_task_point" value="<?php if(isset ($_POST['input_task_point'])){ echo $_POST['input_task_point']; } ?>" readonly>

                                                    <?php //if(isset ($error['input_task_point'])){ say($error['input_task_point']); } ?>
                                                </div>

                                                <div class="form-group col-md-6">
                                                  <label>Number of workers required <span class="text-danger">*</span></label>

                                                  <input type="number" min="1"  id="quantity" class="form-control" name="quantity" value="<?= isset ($_POST['quantity']) ? $_POST['quantity'] : '1'; ?>">

                                                  <?php //if(isset ($error['quantity'])){  say($error['quantity']); } ?>

                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Farm Location <span class="text-danger">*</span></label>

                                                    <input type="number" class="form-control" placeholder="Enter Expiry Percentage" name="farm_location" value="<?php if(isset ($_POST['input_expiry_percentage'])){ echo $_POST['input_expiry_percentage']; } ?>" readonly>

                                                    <?php //if(isset ($error['input_expiry_percentage'])){ say($error['input_expiry_percentage']); } ?>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label>State <span class="text-danger">*</span></label>

                                                    <select class="js-example-basic-single form-control select2-hidden-accessible" name="input_task_status">
                                                      <option value="">--Select State--</option>
                                                      <?php foreach ($nigeria as $key => $value): ?>
                                                        <option value="<?= $value ?>"><?= $value ?></option>
                                                      <?php endforeach; ?>
                                                    </select>

                                                    <?php if(isset ($error['input_task_status'])){ say($error['input_task_status']); } ?>
                                                </div>

                                                <div class="form-group col-md-6">
                                                    <label>Farm Name <span class="text-danger">*</span></label>

                                                    <input type="text" class="form-control" placeholder="Enter Task Expiry" name="dated_task_expiry" value="<?php if(isset ($_POST['dated_task_expiry'])){ echo $_POST['dated_task_expiry']; } ?>">

                                                    <?php //if(isset ($error['dated_task_expiry'])){ say($error['dated_task_expiry']); } ?>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>Crops Produced <span class="text-danger">*</span></label>

                                                    <input type="text" id="taskExpiry" class="form-control" placeholder="Enter Task Expiry" name="dated_task_expiry" value="<?php if(isset ($_POST['dated_task_expiry'])){ echo $_POST['dated_task_expiry']; } ?>">

                                                    <?php //if(isset ($error['dated_task_expiry'])){ say($error['dated_task_expiry']); } ?>
                                                </div>
                                                <div class="form-group col-md-6">
                                                    <label>When are workers need?<span class="text-danger">*</span></label>

                                                    <input type="date" id="taskExpiry" class="form-control" placeholder="Enter Task Expiry" name="dated_task_expiry" value="<?php if(isset ($_POST['dated_task_expiry'])){ echo $_POST['dated_task_expiry']; } ?>">

                                                    <?php if(isset ($error['dated_task_expiry'])){ say($error['dated_task_expiry']); } ?>
                                                </div>
                                                <div class="form-group col-md-6" >
                                                    <label for="">Payment Method <span class="text-danger">*</span></label>
                                                    <select class="js-example-basic-single form-control select2-hidden-accessible" name="title">
                                                      <option value="">--select Service--</option>
                                                      <option value="Card Payment">Card Payment</option>
                                                      <option value="Direct Bank Transfer">Direct Bank Transfer</option>
                                                    </select>
                                                </div>

                                              </div>
                                              <button name="submit" type="submit" class="btn btn-primary">Create Invoice</button>
                                            </form>
                                          <!-- </div> -->

                                          <!-- <div class="col-md-4">

                                          </div>
                                        </div> -->
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
 </div>
 <script src="/da/assets/plugins/select2/js/select2.full.min.js"></script>
 <script src="/da/assets/js/pages/form-select-custom.js"></script>
