<?php

$request_headers = getallheaders();
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
  http_response_code(502);
  die;
}

if (!in_array($request_headers['Host'], $headersName)) {
  http_response_code(502);
  die;
}


$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json, true);
// die(var_dump($data));

if (!isset($data['data']) || !isset($data['where']) || !isset($data['values'])) {
  http_response_code(400);
  die();
}

$password = getenv("EMAIL_PASSWORD");
function SendMail2admin($title, $recipient, $msg, $email, $pword)
{

  $sit_email = $email;

  $to = $recipient;

  // require APP_PATH.'/phpm/PHPMailerAutoload.php';
  // If necessary, modify the path in the require statement below to refer to the
  // location of your Composer autoload.php file.
  // require 'phpm/PHPMailerAutoload.php';

  // Instantiate a new PHPMailer
  $mail = new PHPMailer;
  // $mail->SMTPDebug = 3;

  // Tell PHPMailer to use SMTP
  $mail->isSMTP();

  // Replace sender@example.com with your "From" address.
  // This address must be verified with Amazon SES.
  $mail->setFrom($sit_email, $title);
  $mail->AddReplyTo($sit_email, $title);

  // Replace recipient@example.com with a "To" address. If your account
  // is still in the sandbox, this address must be verified.
  // Also note that you can include several addAddress() lines to send
  // email to multiple recipients.
  $mail->addAddress($to);

  // Replace smtp_username with your Amazon SES SMTP user name.
  $mail->Username = $sit_email;

  // Replace smtp_password with your Amazon SES SMTP password.
  $mail->Password = $pword;
  // die(var_dump($mail->Password));

  // Specify a configuration set. If you do not want to use a configuration
  // set, comment or remove the next line.
  // $mail->addCustomHeader('X-SES-CONFIGURATION-SET', 'ConfigSet');

  // If you're using Amazon SES in a region other than US West (Oregon),
  // replace email-smtp.us-west-2.amazonaws.com with the Amazon SES SMTP
  // endpoint in the appropriate region.
  $mail->Host = "smtp.gmail.com";

  // The subject line of the email
  $subject = "New Task Submit";
  $mail->Subject = $subject;

  // The HTML-formatted body of the email
  $mail->Body = " ";

  $mail->Body .= "<p>" . $msg . "</p>";

  // Tells PHPMailer to use SMTP authentication
  $mail->SMTPAuth = true;

  // Enable TLS encryption over port 587
  $mail->SMTPSecure = 'tls';
  $mail->Port = 587;

  // Tells PHPMailer to send HTML-formatted email
  $mail->isHTML(true);

  // The alternative email body; this is only displayed when a recipient
  // opens the email in a non-HTML email client. The \r\n represents a
  // line break.
  // $mail->AltBody = "Do not send a reply to this mail";

  // if(!$mail->send()) {
  //    // return "EMAIL_SENDING_FAILED._INFO: " . $mail->ErrorInfo;
  //    // return "Failed";
  //  } else {
  //    // return "Sent";
  //  }
  $mail->send();
}

$columnWhere = $data['where'];
$user = [];
foreach ($data['where'] as $key => $value) {
  $user[] = $value;
}
$table = $data['data'];
// $parameters = $data['values'];
$para = $data['values'];

$parameters = [];
$taskk = [];
$task_id = [];

foreach ($para as $key => $value) {
  $parameters[$key] = serialize($para[$key]);
  $taskk[] = $para[$key];
}

foreach ($taskk[0] as $key => $value) {
  $task_id[] = $key;
}
// die(var_dump($task_id[0]));
$result = [];

try {
  //check for responded members on tasks record
  $check_responded_members = selectContent($conn, "panel_tasks", ['hash_id' => $task_id[0]]);

  // array_pop($parameters);
  $what = formatParam($parameters);
  $vall = formatPutWhere($columnWhere);

  // var_dump($parameters);
  $sql = sprintf(
    'UPDATE %s SET %s',
    $table,
    $what
  );
  $sql .= " WHERE " . $vall;
  // //die(var_dump($sql));
  $stmt =  $conn->prepare($sql);
  // var_dump($sql);
  $columnWhere = formatWhereParam($columnWhere);
  // var_dump($columnWhere);

  $usr = selectContent($conn, "panel_members", ["hash_id" => $user[0]]);
  $usr_name = $usr[0]['input_firstname'] . " " . $usr[0]['input_lastname'];
  $team = $usr[0]['select_team'];
  $admin = selectContent($conn, "panel_members", ["select_team" => $team, "admin" => 1]);
  $admin_email = $admin[0]['input_email'];

  $task_link = "http://192.168.33.43/task?id=" . $task_id[0];
  $message = "Dear <b>" . $admin[0]['input_firstname'] . "</b>, a member (<b>" . $usr_name . "</b>) in your team has submitted his/her task <br><br><br>Click on the link below to see details <br><br> <b>$task_link</b>";


  require APP_PATH . '/phpm/PHPMailerAutoload.php';

  //check if the collumn is null or not
  $check = selectContent($conn, $table, $data['where']);

  if (is_null($check[0]['submit_and_validate_task'])) {
    // die(var_dump($columnWhere));
    $newt = $parameters + $columnWhere;
    $stmt->execute($newt);
    // die(var_dump($columnWhere));

    //submit members_id in task table
    if (is_null($check_responded_members[0]['responded_members'])) {
      $res_user[] = $user[0];
      $set['responded_members'] = serialize($res_user);
      $whr['hash_id'] = $task_id[0];
      updateContent($conn, "panel_tasks", $set, $whr);
    } else {
      $responded = unserialize($check_responded_members[0]['responded_members']);
      $res_user[] = $user[0];
      $total_res_users = array_merge($responded, $res_user);
      $set['responded_members'] = serialize($total_res_users);
      $whr['hash_id'] = $task_id[0];
      updateContent($conn, "panel_tasks", $set, $whr);
    }

    //send mail
    SendMail2admin($site_name, $admin_email, $message, $site_email, $password);

    $result["success"] = true;
  } else {
    $responded_tasks = unserialize($check[0]['submit_and_validate_task']);

    //check if task has been submitted before
    $submitted_tasks = [];
    foreach ($responded_tasks as $key => $value) {
      $submitted_tasks[] = $key;
    }

    if (in_array($task_id[0], $submitted_tasks)) {
      $result['msg'] = true;
      $result['message'] = "You have submitted this task before now";
    } else {
      $tasks = $responded_tasks + $para['submit_and_validate_task'];
      $new_tasks = serialize($tasks);
      $build_tasks = [];
      $build_tasks['submit_and_validate_task'] = $new_tasks;


      $newt = $build_tasks + $columnWhere;
      $stmt->execute($newt);

      //submit members_id in task table
      if (is_null($check_responded_members[0]['responded_members'])) {
        $res_user[] = $user[0];
        $set['responded_members'] = serialize($res_user);
        $whr['hash_id'] = $task_id[0];
        updateContent($conn, "panel_tasks", $set, $whr);
      } else {
        $rss = [];
        $ress_members = unserialize($check_responded_members[0]['responded_members']);
        foreach ($ress_members as $key => $value) {
          $rss[] = $value;
        }
        //check if the user has responded before now, probably because his/her task has been revoked
        if (!in_array($user[0], $rss)) {
          $responded = unserialize($check_responded_members[0]['responded_members']);
          $res_user[] = $user[0];
          $total_res_users = array_merge($responded, $res_user);
          $set['responded_members'] = serialize($total_res_users);
          $whr['hash_id'] = $task_id[0];
          updateContent($conn, "panel_tasks", $set, $whr);
        }
      }

      //send mail
      SendMail2admin($site_name, $admin_email, $message, $site_email, $password);

      $result["success"] = true;
      // die(var_dump($newt));
    }
  }
} catch (PDOException $e) {
  die($e);
  http_response_code(409);
  $result["error"] = true;
  die;
}

$res = json_encode($result);
echo $res;


die;
