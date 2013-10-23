<?php

require_once ('../vendor/icalforce/oauth.inc.php');
require_once ('../config/whitelist.php');


if (! isset($_POST['signed_request'])) {
  header('HTTP/1.1 403 Forbidden');
  echo 'Forbidden';
  exit();
}

$signedRequest = $_REQUEST['signed_request'];

if ($signedRequest == null) {
  header('HTTP/1.1 403 Forbidden');
  echo 'Forbidden';
  exit();
}


//decode the signedRequest
$sep = strpos($signedRequest, '.');
$encodedSig = substr($signedRequest, 0, $sep);
$encodedEnv = substr($signedRequest, $sep + 1);
$calcedSig = base64_encode(hash_hmac("sha256", $encodedEnv, $CLIENT_SECRET, true));          

if ($calcedSig != $encodedSig) {
  header('HTTP/1.1 403 Forbidden');
  echo 'Forbidden';
  exit();
}


//decode the signed request object
$response = json_decode(base64_decode($encodedEnv), true);


function getUser($id, $instance_url, $access_token) {
  $url = "$instance_url/services/data/v20.0/sobjects/User/$id";

  $curl = curl_init($url);
  curl_setopt($curl, CURLOPT_HEADER, false);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_HTTPHEADER, array("Authorization: OAuth $access_token"));

  $json_response = curl_exec($curl);
  $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);

  $ret = array('status' => $status, 'url' => $url, 'raw' => $json_response);
  if ( $status != 200 ) {
    $ret['error'] = curl_error($curl);
  }

  curl_close($curl);

  $ret['response'] = json_decode($json_response, true);
  
  return $ret;
}


$uid = $response['context']['user']['userId'];
$uid15 = substr($uid, 0, 15);
//$ret = getUser($uid, $response['client']['instanceUrl'], $response['client']['oauthToken']);


$hasCalendar = false;
$hasSecret = false;
$calendarUrl = '';
$secretUrl = '';

if (isset($ICALFORCEWHITELIST_19b70db3_f172_40eb_910c_f356365166c1)) {
  foreach ($ICALFORCEWHITELIST_19b70db3_f172_40eb_910c_f356365166c1 as $u15 => $userRec) {
    if ($u15 == $uid15) {
      $hasCalendar = true;
      $calendarUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/calendar.php?t=' . $userRec['pub-token'];
      $secretUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/private/secret.php?u=' . $uid15;
      break;
    }
  }
} else {
  $hasSecret = true;
  $secretUrl = 'https://' . $_SERVER['HTTP_HOST'] . '/private/secret.php?u=' . $uid15;
}




?><!DOCTYPE html>
<head>
  <title>My Calendar</title>
</head>
<body>
  <?php if($hasCalendar): ?>
  <p>
    <h1>Calendar URL</h1>
    <a target="_blank" href="<?php echo $calendarUrl ?>"><?php echo $calendarUrl ?></a>
  </p>
  <?php elseif($hasSecret): ?>
  <p>
    <h1>Secret URL</h1>
    <a target="_blank" href="<?php echo $secretUrl ?>"><?php echo $secretUrl ?></a>
  </p>
  <?php else: ?>
  <p>
    <h1>You have no calendar URL.</h1>
  </p>
  <?php endif; ?>
</body>