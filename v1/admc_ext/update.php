<?php
$request_headers = getallheaders();
if($_SERVER['REQUEST_METHOD'] !=="POST"){
   http_response_code(502);
   die;
}
$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json,true);
$result = [];
try {
  $getid = $data['id'];
  // $status = $data['status'];

  updateContent($conn, "invoice", ["status" => "paid"] , ["hash_id" => $getid]);
  $result['success'] = true;
} catch (\Exception $e) {
  $result['error'] = true;
}

$res = json_encode($result);
echo $res;



 ?>
