<?php 
$request_headers = getallheaders();
if($_SERVER['REQUEST_METHOD'] !=="POST"){
   http_response_code(502);
   die;
}

if( !in_array($request_headers['Host'],$headersName) ){
  http_response_code(502);
  die;
}

$json = file_get_contents('php://input');
$data = json_decode($json,true);
extract($data);

$email_subject = "Contact - Mckodev";
$userMessage = "Dear $name, We are glad to receive your message, we'll get in touch with you soonest.";

$result = [];

try{
  require APP_PATH.'/phpm/PHPMailerAutoload.php';

//Message for User
// Create a new PHPMailer instance
$mail = new PHPMailer;
$mail->isSMTP(); // Set mailer to use SMTP
// $mail->SMTPDebug = 2; // Enable verbose debug output
$mail->Host = $site_email_smtp_host; // Specify main and backup SMTP servers
$mail->SMTPAuth = true; // Enable SMTP authentication
$mail->Username = $site_email_from; // SMTP username
$mail->Password = $site_email_password; // SMTP password
$mail->SMTPSecure = $site_email_smtp_secure_type;; // Enable TLS encryption, [ICODE]ssl[/ICODE] also accepted
$mail->Port = $site_email_smtp_port;
$mail->setFrom($site_email, $site_name);
// $mail->addAddress($email, $name); // Add a recipient
$mail->addAddress($email); // Name is optional
$mail->addReplyTo($site_email, $site_name);
$mail->isHTML(true); // Set email format to HTML
$mail->Subject = "Event Inquiry - " . $event_date;
$mail->Body = $userMessage;
$mail->AltBody = "Do not send a reply to this mail";


$mail2admin = new PHPMailer;
$mail2admin->isSMTP(); // Set mailer to use SMTP
// $mail2admin->SMTPDebug = 2; // Enable verbose debug output
$mail2admin->Host = $site_email_smtp_host; // Specify main and backup SMTP servers
$mail2admin->SMTPAuth = true; // Enable SMTP authentication
$mail2admin->Username = $site_email_from; // SMTP username
$mail2admin->Password = $site_email_password; // SMTP password
$mail->SMTPSecure = $site_email_smtp_secure_type;; // Enable TLS encryption, [ICODE]ssl[/ICODE] also accepted
$mail->Port = $site_email_smtp_port;
$mail2admin->setFrom($site_email, $site_name);
// $mail->addAddress($email, $name); // Add a recipient
$mail2admin->addAddress($site_email); // Name is optional
$mail2admin->addReplyTo($site_email, $site_name);
$mail2admin->isHTML(true); // Set email format to HTML
$mail2admin->Subject = "New Event Inquiry - " . $event_date;
$mail2admin->Body = "
<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
    <h2 style='color: #1e3737; border-bottom: 2px solid #ffa415; padding-bottom: 10px;'>New Event Inquiry</h2>
    <table style='width: 100%; border-collapse: collapse; margin-top: 15px;'>
";

$mail2admin->AltBody = "New Event Inquiry";

if (isset($name) && !empty($name)) {
    $mail2admin->Body.="<tr><td style='padding: 10px; font-weight: bold; color: #333; border-bottom: 1px solid #eee; width: 40%;'>Name</td><td style='padding: 10px; color: #555; border-bottom: 1px solid #eee;'>".$name."</td></tr>";
}
if (isset($email) && !empty($email)) {
    $mail2admin->Body.="<tr><td style='padding: 10px; font-weight: bold; color: #333; border-bottom: 1px solid #eee;'>Email</td><td style='padding: 10px; color: #555; border-bottom: 1px solid #eee;'><a href='mailto:".$email."'>".$email."</a></td></tr>";
}
if (isset($phone) && !empty($phone)) {
    $mail2admin->Body.="<tr><td style='padding: 10px; font-weight: bold; color: #333; border-bottom: 1px solid #eee;'>Phone</td><td style='padding: 10px; color: #555; border-bottom: 1px solid #eee;'><a href='tel:".$phone."'>".$phone."</a></td></tr>";
}
if (isset($event_type) && !empty($event_type)) {
    $mail2admin->Body.="<tr><td style='padding: 10px; font-weight: bold; color: #333; border-bottom: 1px solid #eee;'>Type of Event</td><td style='padding: 10px; color: #555; border-bottom: 1px solid #eee;'>".$event_type."</td></tr>";
}
if (isset($guest_count) && !empty($guest_count)) {
    $mail2admin->Body.="<tr><td style='padding: 10px; font-weight: bold; color: #333; border-bottom: 1px solid #eee;'>Number of Guests</td><td style='padding: 10px; color: #555; border-bottom: 1px solid #eee;'>".$guest_count."</td></tr>";
}
if (isset($location) && !empty($location)) {
    $mail2admin->Body.="<tr><td style='padding: 10px; font-weight: bold; color: #333; border-bottom: 1px solid #eee;'>Event Location</td><td style='padding: 10px; color: #555; border-bottom: 1px solid #eee;'>".$location."</td></tr>";
}
if (isset($event_date) && !empty($event_date)) {
    $mail2admin->Body.="<tr><td style='padding: 10px; font-weight: bold; color: #333; border-bottom: 1px solid #eee;'>Preferred Date</td><td style='padding: 10px; color: #555; border-bottom: 1px solid #eee;'>".$event_date."</td></tr>";
}
if (isset($service_required) && !empty($service_required)) {
    $mail2admin->Body.="<tr><td style='padding: 10px; font-weight: bold; color: #333; border-bottom: 1px solid #eee;'>Service Required</td><td style='padding: 10px; color: #555; border-bottom: 1px solid #eee;'>".$service_required."</td></tr>";
}

$mail2admin->Body.="</table>";

if (isset($message) && !empty($message)) {
    $mail2admin->Body.="<div style='margin-top: 20px; padding: 15px; background-color: #f9f9f9; border-left: 3px solid #ffa415;'><h4 style='margin: 0 0 10px; color: #333;'>Event Brief</h4><p style='color: #555; line-height: 1.6; margin: 0;'>".nl2br(htmlspecialchars($message))."</p></div>";
}

$mail2admin->Body.="</div>";



if($mail->send() && $mail2admin->send()) {
  $result['success'] = "Message Sent Successfully, we'll get in touch with you soonest.";
}else{
  $result['failed'] = "Sorry, an error occured, please try again.";
}
$result['sent']="here is the main thing";

}catch(Exception $e){
  // echo $e->getMessage();
  $result['failed'] = $e;
}

$result = json_encode($result);
echo $result;

?>
