<?php



function MCK_Clarity()
{
  $clarityId = getenv("CLARITY_ID");

  if (empty($clarityId)) {
    // $clarityId = "r4pvm9vn9y";
    return false;
  }

  echo "
  <script type='text/javascript'>
    (function(c,l,a,r,i,t,y){
        c[a]=c[a]||function(){(c[a].q=c[a].q||[]).push(arguments)};
        t=l.createElement(r);t.async=1;t.src='https://www.clarity.ms/tag/'+i;
        y=l.getElementsByTagName(r)[0];y.parentNode.insertBefore(t,y);
      })(window, document, 'clarity', 'script', '{$clarityId}');
  </script>
  ";
}

function MCK_INSIGHT()
{
  $projectKey = getenv("OPENREPLAY_PROJECT_KEY");
  if (empty($projectKey)) {
    $projectKey = "IIHuO7AZtUZOCaOr86Ks";
  }
  echo "
<script>
  var initOpts = {
    projectKey: '{$projectKey}',
    ingestPoint: 'https://insight.mckodev.com.ng/ingest',
    defaultInputMode: 2,
    obscureTextNumbers: false,
    obscureTextEmails: true,
  };
  var startOpts = { userID: '' };
  (function(A,s,a,y,e,r){
    r=window.OpenReplay=[e,r,y,[s-1, e]];
    s=document.createElement('script');s.src=A;s.async=!a;
    document.getElementsByTagName('head')[0].appendChild(s);
    r.start=function(v){r.push([0])};
    r.stop=function(v){r.push([1])};
    r.setUserID=function(id){r.push([2,id])};
    r.setUserAnonymousID=function(id){r.push([3,id])};
    r.setMetadata=function(k,v){r.push([4,k,v])};
    r.event=function(k,p,i){r.push([5,k,p,i])};
    r.issue=function(k,p){r.push([6,k,p])};
    r.isActive=function(){return false};
    r.getSessionToken=function(){};
  })('//static.openreplay.com/16.0.1/openreplay.js',1,0,initOpts,startOpts);
</script>
  ";
}




function ADMCGoogleAnalytics()
{
  $googleAnalyticsId = getenv("GOOGLE_ANALYTICS_ID");
  if (empty($googleAnalyticsId)) {
    return false;
  }

  echo "
  <script async src='https://www.googletagmanager.com/gtag/js?id={$googleAnalyticsId}'></script>
  <script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());
  gtag('set', 'user_properties', {'domain': window.location.hostname});
  gtag('config', '{$googleAnalyticsId}');
  </script>
  ";

  return true;
}


if (!function_exists('getallheaders')) {
  function getallheaders()
  {
    $headers = [];
    foreach ($_SERVER as $name => $value) {
      if (substr($name, 0, 5) == 'HTTP_') {
        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
      }
    }
    return $headers;
  }
}

function cors()
{

  // Allow from any origin
  if (isset($_SERVER['HTTP_ORIGIN'])) {
    // Decide if the origin in $_SERVER['HTTP_ORIGIN'] is one
    // you want to allow, and if so:
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');    // cache for 1 day
  }

  // Access-Control headers are received during OPTIONS requests
  if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
      // may also be using PUT, PATCH, HEAD etc
      header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

    if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
      header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    exit(0);
  }
}


function decodeDate($date)
{
  $split = explode('-', $date);
  $month = $split[1];
  $day = $split[2];
  $year = $split[0];
  if ($month == 1) {
    $month = "January";
  }
  if ($month == 2) {
    $month = "February";
  }
  if ($month == 3) {
    $month = "March";
  }
  if ($month == 4) {
    $month = "April";
  }
  if ($month == 5) {
    $month = "May";
  }
  if ($month == 6) {
    $month = "June";
  }
  if ($month == 7) {
    $month = "July";
  }
  if ($month == 8) {
    $month = "August";
  }
  if ($month == 9) {
    $month = "September";
  }
  if ($month == 10) {
    $month = "October";
  }
  if ($month == 11) {
    $month = "November";
  }
  if ($month == 12) {
    $month = "December";
  }
  $newDate = $month . ' ' . $day . ', ' . $year;
  return $newDate;
}
function ForumInfo($dbconn, $sess)
{
  $stmt = $dbconn->prepare("SELECT * FROM users WHERE hash_id = :sid");
  $data = [
    ':sid' => $sess
  ];
  $stmt->execute($data);
  $row = $stmt->fetch(PDO::FETCH_BOTH);
  return $row;
}
function insert($conn, $table, $parameters)
{

  array_pop($parameters);
  // var_dump($parameters);
  $sql = sprintf(
    'INSERT INTO %s (%s) VALUES(%s)',
    $table,
    implode(', ', array_keys($parameters)),
    ':' . implode(',:', array_keys($parameters))
  );
  // //die(var_dump($sql));
  $stmt =  $conn->prepare($sql);
  $stmt->execute($parameters);
}
// function displayErrors($error, $field)
// {
//   $result= "";
//   if (isset($error[$field]))
//   {
//     $result = '<span style="color:red">'.$error[$field].'</span>';
//   }
//   return $result;
// }

function columnSummation($conn, $column, $table)
{
  $stmt = $conn->prepare("SELECT $column FROM $table");
  $stmt->execute();
  $plus = 0;
  // die(var_dump($row = $stmt->fetch(PDO::FETCH_BOTH)));
  while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
    $plus += $row[$column];
  }
  // die(var_dump($plus));
  return $plus;
}
function columnPrySummation($conn, $column, $table)
{
  $pry = "primary";
  $stmt = $conn->prepare("SELECT $column FROM $table WHERE category=:pry");

  $stmt->bindParam(":pry", $pry);
  $stmt->execute();
  $plus = 0;
  // die(var_dump($row = $stmt->fetch(PDO::FETCH_BOTH)));
  while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
    $plus += $row[$column];
  }
  // die(var_dump($plus));
  return $plus;
}

function totalCount($conn, $table)
{
  $stmt = $conn->prepare("SELECT * FROM $table");
  $stmt->execute();
  return $stmt->rowCount();
}
function totalPryCount($conn, $table)
{
  $pry = "primary";
  $stmt = $conn->prepare("SELECT * FROM $table WHERE category=:pry");
  $stmt->bindParam(":pry", $pry);
  $stmt->execute();
  return $stmt->rowCount();
}

function totalCategoryCount($conn, $table, $column, $value)
{
  $stmt = $conn->prepare("SELECT * FROM $table WHERE $column=:value");
  $stmt->bindParam(':value', $value);
  $stmt->execute();
  return $stmt->rowCount();
}

function cleans($string)
{
  $string = str_replace(array('[\', \']'), '', $string);
  $string = preg_replace('/\[.*\]/U', '', $string);
  $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
  $string = htmlentities($string, ENT_COMPAT, 'utf-8');
  $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string);
  $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/'), '-', $string);
  return strtolower(trim($string, '-'));
}









function cleanTime($time)
{
  $timestamp = strtotime($time) + 60 * 60;

  $time = date('H:i:s', $timestamp);
  return $time;
}
function getForumCategory($dbconn)
{
  $stmt = $dbconn->prepare("SELECT * FROM category");
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
    extract($row);

    echo '<li><a href="/category/' . $hash_id . '">' . $category_name . ' &nbsp;<span class="badge pull-right">' . categoryCount($dbconn, $row['hash_id']) . '</span></a></li>';
  }
}
function getMyForum($dbconn, $id)
{
  $stmt = $dbconn->prepare("SELECT * FROM topic WHERE user_id=:hid");
  $stmt->bindParam(":hid", $id);
  $stmt->execute();
  while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
    extract($row);
    $post = cleans($title);
    echo '<div class="divline"></div>
    <div class="blocktxt">
    <a href="/topic/' . $post . '-' . $row['hash_id'] . '">' . strtoupper(strtolower($row['title'])) . '</a>
    </div>';
  }
}

function previewBody($string, $count)
{
  $original_string = $string;
  $words = explode(' ', $original_string);
  if (count($words) > $count) {
    $words = array_slice($words, 0, $count);
    $string = implode(' ', $words) . "...";
  }
  return strip_tags($string);
}


function insertSafe($conn, $table, $parameters)
{
  try {
    // array_pop($parameters);
    // var_dump($parameters);
    $sql = sprintf(
      'INSERT INTO %s (%s) VALUES(%s)',
      $table,
      implode(', ', array_keys($parameters)),
      ':' . implode(',:', array_keys($parameters))
    );
    // //die(var_dump($sql));
    $stmt =  $conn->prepare($sql);
    $stmt->execute($parameters);
  } catch (PDOException $e) {
    die($e);
    die("Error: Try again After Some Times");
  }
}
function insertContent($conn, $table, $parameters)
{
  try {

    // var_dump($parameters);
    $sql = sprintf(
      'INSERT INTO %s (%s) VALUES(%s)',
      $table,
      implode(', ', array_keys($parameters)),
      ':' . implode(',:', array_keys($parameters))
    );
    // //die(var_dump($sql));
    $stmt =  $conn->prepare($sql);
    $stmt->execute($parameters);
  } catch (PDOException $e) {
    // die($e);
    die("Error: Try again After Some Times");
  }
}

// function update($dbconn, $table, $parameters,$column,$value,$locat){
//
//
// try {
//   function getVal($param){
//   $result = [];
//   foreach($param as $col => $val){
//       $result[] = "$col = :$col";
//     }
//     $new = implode(', ', $result);
//     return $new;
// }
//   function getVal2($param){
//   $result = [];
//   foreach($param as $col => $val){
//       $result[] = "$col = :$col";
//     }
//     $new = implode(' AND ', $result);
//     return $new;
// }
//
//
// array_pop($parameters);
// $what = getVal($parameters);
// $vall = getVal2($value);
//
//   // var_dump($parameters);
//   $sql = sprintf('UPDATE %s SET %s',
//       $table, $what
//   );
//   $sql .= " WHERE ".$vall;
//   // //die(var_dump($sql));
// $stmt =  $dbconn->prepare($sql);
// $newt = $parameters + $value;
// // die(var_dump($newt));
// $stmt->execute($newt);
// } catch (PDOException $e) {
//   die($e);
// }
//
// if($table == "admin"){
//   $success = "Profile Successfully Edited";
//   $succ = preg_replace('/\s+/', '_', $success);
//   header("Location:/$locat");
// }else {
//   $success = "Edited";
//   $succ = preg_replace('/\s+/', '_', $success);
//   header("Location:/$locat?success=$succ");
// }
//
//
//
// }


// function compressImage($files, $name, $quality, $upDIR ) {
//   // die(var_dump($files[$name]['type']));
//   $rnd = rand(0000000, 9999999);
//   $strip_name = preg_replace("/[^.a-zA-Z0-9]/", "_",$_FILES[$name]['name'] );
//   $filename = time()."mail".$strip_name;
//   $destination_url = $upDIR.$filename;
//   $info = getimagesize($files[$name]['tmp_name']);
//   if ($info['mime'] == 'image/jpeg')
//   $image = imagecreatefromjpeg($files[$name]['tmp_name']);
//   elseif ($info['mime'] == 'image/gif')
//   $image = imagecreatefromgif($files[$name]['tmp_name']);
//   elseif ($info['mime'] == 'image/png')
//   $image = imagecreatefrompng($files[$name]['tmp_name']);
//   imagejpeg($image, $destination_url, $quality);
//   $img['upload'] = $destination_url;
//   return $img;
// }
function compressImage2($files, $name, $quality, $upDIR)
{
  // die(var_dump($files[$name]['type']));

  $rnd = rand(0000000, 9999999);
  $strip_name = preg_replace("/[^.a-zA-Z0-9]/", "_", $_FILES[$name]['name']);
  $filename = time() . "mail" . $strip_name;
  $destination_url = $upDIR . $filename;
  $thumb_url = 'thumbs/' . $filename;
  $info = getimagesize($files[$name]['tmp_name']);
  if ($info['mime'] == 'image/jpeg')
    $image = imagecreatefromjpeg($files[$name]['tmp_name']);
  elseif ($info['mime'] == 'image/gif')
    $image = imagecreatefromgif($files[$name]['tmp_name']);
  elseif ($info['mime'] == 'image/png')
    $image = imagecreatefrompng($files[$name]['tmp_name']);


  imagejpeg($image, $destination_url, 90);
  imagejpeg($image, $thumb_url, 30);
  $img['upload'] = $destination_url;
  $img['thumb'] = $thumb_url;
  return $img;
}

function selectTableContent2($dbconn, $table, $column, $columnWhere)
{
  $vall = formatWhere($columnWhere);
  $column = implode(',', $column);
  try {

    // $what = getVal($parameters);

    // var_dump($parameters);
    $sql = sprintf(
      'SELECT %s FROM %s',
      $column,
      $table
    );

    if (count($columnWhere) > 0) {
      $sql .= " WHERE " . $vall;
    }

    // die(var_dump($sql));
    $stmt =  $dbconn->prepare($sql);
    $newt = $columnWhere;
    // die(var_dump($newt));
    if (count($columnWhere) > 0) {
      $stmt->execute($newt);
    } else {
      $stmt->execute();
    }

    // $result = [];
    $row = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);




    return $row;
  } catch (PDOException $e) {
    die($e);
  }
}

function deleteContent($dbconn, $table, $columnWhere)
{

  // die($columnWhere);
  try {

    // $what = getVal($parameters);
    $vall = formatWhere($columnWhere);

    // var_dump($parameters);
    $sql = sprintf(
      'DELETE FROM %s',
      $table
    );
    $sql .= " WHERE " . $vall;


    //die(var_dump($sql));
    $stmt =  $dbconn->prepare($sql);
    $newt = $columnWhere;
    // die(var_dump($newt));

    $stmt->execute($newt);
  } catch (PDOException $e) {
    die($e);
  }
}

function selectContent($dbconn, $table, $columnWhere)
{
  $vall = formatWhere($columnWhere);
  try {

    // $what = getVal($parameters);

    // var_dump($parameters);
    $sql = sprintf(
      'SELECT * FROM %s',
      $table
    );

    if (count($columnWhere) > 0) {
      $sql .= " WHERE " . $vall;
    }

    //die(var_dump($sql));
    $stmt =  $dbconn->prepare($sql);
    $newt = $columnWhere;
    // die(var_dump($newt));
    if (count($columnWhere) > 0) {
      $stmt->execute($newt);
    } else {
      $stmt->execute();
    }

    $result = [];
    while ($row = $stmt->fetch(PDO::FETCH_BOTH)) {
      $result[] = $row;
    }

    return $result;
  } catch (PDOException $e) {
    die($e);
  }
}
function selectContentDesc($dbconn, $table, $columnWhere, $order, $limit)
{
  $vall = formatWhere($columnWhere);
  try {

    // $what = getVal($parameters);

    // var_dump($parameters);
    $sql = sprintf(
      'SELECT * FROM %s',
      $table
    );

    if (count($columnWhere) > 0) {
      $sql .= " WHERE " . $vall;
    }
    $sql .= " ORDER BY " . $order . " DESC LIMIT " . $limit;

    //die(var_dump($sql));
    $stmt =  $dbconn->prepare($sql);
    $newt = $columnWhere;
    // die(var_dump($newt));
    if (count($columnWhere) > 0) {
      $stmt->execute($newt);
    } else {
      $stmt->execute();
    }

    $result = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $result[] = $row;
    }

    return $result;
  } catch (PDOException $e) {
    die($e);
    die($e);
  }
}
function selectContentAsc($dbconn, $table, $columnWhere, $order, $limit)
{
  $vall = formatWhere($columnWhere);
  try {

    // $what = getVal($parameters);

    // var_dump($parameters);
    $sql = sprintf(
      'SELECT * FROM %s',
      $table
    );

    if (count($columnWhere) > 0) {
      $sql .= " WHERE " . $vall;
    }
    $sql .= " ORDER BY " . $order . " ASC LIMIT " . $limit;

    //die(var_dump($sql));
    $stmt =  $dbconn->prepare($sql);
    $newt = $columnWhere;
    // die(var_dump($newt));
    if (count($columnWhere) > 0) {
      $stmt->execute($newt);
    } else {
      $stmt->execute();
    }

    
    $result = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $result[] = $row;
    }

    return $result;
  } catch (PDOException $e) {
    // die($e);
    die($e);
  }
}
function selectTableContent($dbconn, $table, $column, $columnWhere)
{
  $vall = formatWhere($columnWhere);
  $column = implode(',', $column);
  try {

    // $what = getVal($parameters);

    // var_dump($parameters);
    $sql = sprintf(
      'SELECT %s FROM %s',
      $column,
      $table
    );

    if (count($columnWhere) > 0) {
      $sql .= " WHERE " . $vall;
    }

    // die(var_dump($sql));
    $stmt =  $dbconn->prepare($sql);
    $newt = $columnWhere;
    // die(var_dump($newt));
    if (count($columnWhere) > 0) {
      $stmt->execute($newt);
    } else {
      $stmt->execute();
    }

    $result = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $result[] = $row;
    }

    return $result;
  } catch (PDOException $e) {
    die($e);
  }
}


function formatParam($param)
{
  $result = [];
  foreach ($param as $col => $val) {
    $result[] = "$col = :$col";
  }
  $new = implode(', ', $result);
  return $new;
}
function formatWhereParam($param)
{
  $result = [];
  foreach ($param as $col => $val) {
    $cola = $col . "a";
    $result[$cola] = $val;
  }
  // $new = implode(', ', $result);
  return $result;
}
function formatWhere($param)
{
  $result = [];
  foreach ($param as $col => $val) {
    $result[] = "$col = :$col";
  }
  $new = implode(' AND ', $result);
  return $new;
}
function formatPutWhere($param)
{
  $result = [];
  foreach ($param as $col => $val) {
    $result[] = "$col = :$col" . "a";
  }
  $new = implode(' AND ', $result);
  return $new;
}

function updateContent($dbconn, $table, $parameters, $columnWhere)
{
  try {



    // array_pop($parameters);
    $what = formatParam($parameters);
    $columnWhere2 = formatWhereParam($columnWhere);
    $vall = formatPutWhere($columnWhere);

    // var_dump($parameters);
    $sql = sprintf(
      'UPDATE %s SET %s',
      $table,
      $what
    );
    $sql .= " WHERE " . $vall;
    // var_dump($sql);
    $stmt =  $dbconn->prepare($sql);
    $newt = $parameters + $columnWhere2;
    // die(var_dump($newt));
    $stmt->execute($newt);
  } catch (PDOException $e) {
    if (isset($_SESSION['debug'])) {
      die($e);
    } else {
      die("Error: Try again After Some Times");
    }
  }
}




function say($value)
{
  echo "<p style='color:red'>*" . $value . "</p>";
}
function commentCount($dbconn, $hid)
{
  $stmt = $dbconn->prepare("SELECT * FROM reply WHERE topic_id=:ti");
  $stmt->bindParam(":ti", $hid);
  $stmt->execute();
  return $stmt->rowCount();
}
function categoryCount($dbconn, $hid)
{
  $stmt = $dbconn->prepare("SELECT * FROM topic WHERE category=:ti");
  $stmt->bindParam(":ti", $hid);
  $stmt->execute();
  return $stmt->rowCount();
}



function base64url_encode($s)
{
  return str_replace(array('+', '/'), array('-', '_'), base64_encode($s));
}

function base64url_decode($s)
{
  return base64_decode(str_replace(array('-', '_'), array('+', '/'), $s));
}

function authenticate($session, $url)
{
  if (!isset($session)) {
    header("Location: /$url?err=You_have_not_logged_in");
  }
}

function shortContent($content)
{
  $body = $content;
  $string = strip_tags($body);
  if (strlen($string) > 50) {
    $stringCut = substr($string, 0, 50);
    $endPoint = strrpos($stringCut, ' ');
    $string = $endPoint ? substr($stringCut, 0, $endPoint) : substr($stringCut, 0);
    $string .= '...';
  }
  return $string;
}

function currentPage($current_page_url)
{
  $page_url = explode("/", $_SERVER['REQUEST_URI']);

  if ($current_page_url == $page_url[1]) {
    $menu_class = "current";
    echo $menu_class;
  }
}

function myWordWrap($string, $length, $breaker)
{
  $returnString = "";

  $stringArray = explode(' ', $string);
  $stringArray = array_chunk($stringArray, $length);
  // var_dump($stringArray);

  foreach ($stringArray as $key => $value) {
    $returnString .= implode(" ", $value) . $breaker;
  }

  return $returnString;
}

function hexToRgb($hex)
{
  $returnString = "";
  // Remove the # symbol if present
  $hex = str_replace("#", "", $hex);

  // Convert the hexadecimal value to decimal values
  $r = hexdec(substr($hex, 0, 2));
  $g = hexdec(substr($hex, 2, 2));
  $b = hexdec(substr($hex, 4, 2));

  $returnString = "$r, $g, $b";
  // Return the RGB color code
  return $returnString;
}
// $word = myWordWrap("Hello my name is israel", 2, "<br/>");
// var_dump($word);  
// die();

if (!function_exists('getallheaders')) {
  function getallheaders()
  {
    $headers = [];
    foreach ($_SERVER as $name => $value) {
      if (substr($name, 0, 5) == 'HTTP_') {
        $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
      }
    }
    return $headers;
  }
}

if (!function_exists('previewBodyWithElipsces')) {

  function previewBodyWithElipsces($string, $count = 50, $strip = true, $url_param = ['link' => '', 'color' => "blue"])
  {
    if ($strip == true) {
      $string = strip_tags($string);
    }

    $original_string = $string;

    // var_dump($original_string);

    $words = explode(' ', $original_string);

    if (isset($url_param['color'])) {
      $url_color = "style='color:" . $url_param['color'] . " !important;'";
    } else {
      $url_color = "";
    }

    if (isset($url_param['text'])) {
      // code...
      $url_text = $url_param['text'];
    } else {
      $url_text = "read more";
    }

    if (isset($url_param['link'])) {
      if (!empty($url_param['link'])) {
        // code...
        $url = " <a href='" . $url_param['link'] . "'" . $url_color . ">" . $url_text . "</a>";
      } else {
        $url = "";
      }
    } else {
      $url = "";
    }


    // var_dump(count($words), $count);
    if (count($words) > $count) {
      $words = array_slice($words, 0, $count);
      $string = implode(' ', $words) . "...";
    }
    return $string . $url;
  }
}

if (!function_exists('stringStartWith')) {
  function stringStartWith($string, $stringToSearch)
  {
    if (strpos($string, $stringToSearch) === 0) {
      return true;
    } else {
      return false;
    }
  }
}
