<?php

require_once ('../vendor/icalforce/oauth.inc.php');
require_once ('../config/whitelist.php');


if (! isset($_COOKIE['V_04660A06A99FEC845360DA2C6D2557A3'])) {
  header('Location: ' . $REDIRECT_URI);
  die('Redirect');
}


$keyInfo = ICalForce\cookieMakeCryptKey();
$cookieDec = ICalForce\cookieDecrypt($keyInfo, $_COOKIE['V_04660A06A99FEC845360DA2C6D2557A3']);
$response = json_decode($cookieDec, true);


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


$uid = explode('/', $response['result']['id']);
$uid = $uid[count($uid) - 1];
$uid15 = substr($uid, 0, 15);
$ret = getUser($uid, $response['result']['instance_url'], $response['result']['access_token']);


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