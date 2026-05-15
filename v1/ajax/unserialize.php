<?php

$request_headers = getallheaders();
if ($_SERVER['REQUEST_METHOD'] !== "POST") {
  http_response_code(502);
  die;
}
// use you local Ip and Domain Name Here
if (!in_array($request_headers['Host'], $headersName)) {
  http_response_code(502);
  die;
}

$json = file_get_contents('php://input');

// Converts it into a PHP object
$data = json_decode($json, true);
// die(print_r($data));

if (!isset($data['value'])) {
  http_response_code(400);
  die();
}

$val = $data['value'];
// $table = $data['data'];
// die(var_dump($data));
$result = [];
// $vall = formatWhere($columnWhere);
// die($vall);
try {

  $result = unserialize($val);
  $res["success"] = true;
  $res["data"] = $result;
} catch (PDOException $e) {
  // die($e);
  http_response_code(409);
  $result["error"] = true;
  die;
}

$res = json_encode($res);
echo $res;


die;
