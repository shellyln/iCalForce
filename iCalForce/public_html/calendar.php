<?php
// $USERNAME - variable that contains your Salesforce.com username (must be in the form of an email)
// $PASSWORD - variable that contains your Salesforce.com password

$USERNAME = $_SERVER['USERNAME'];
$PASSWORD = $_SERVER['PASSWORD'];

$OWNERID  = '123456789012345';

$BASEURL = 'https://ap1.salesforce.com';
if (isset($_SERVER['BASEURL'])) {
  $BASEURL  = $_SERVER['BASEURL'];
}


require_once ('../config/whitelist.php');
require_once ('../vendor/icalforce/icalendar.inc.php');


if (isset($_GET['t'])) {
  if (! isset($ICALFORCEWHITELIST_19b70db3_f172_40eb_910c_f356365166c1)) {
    header('HTTP/1.1 403 Forbidden');
    echo 'no whitelist exists';
    exit();
  }
  
  unset($OWNERID);
  foreach ($ICALFORCEWHITELIST_19b70db3_f172_40eb_910c_f356365166c1 as $uid => $rec) {
    if ($_GET['t'] == $rec['pub-token']) {
      $OWNERID = $uid;
      break;
    }
  }
  if (! isset($OWNERID)) {
    header('HTTP/1.1 403 Forbidden');
    echo 'bad pub token';
    exit();
  }
} else {
  header('HTTP/1.1 403 Forbidden');
  echo 'disallowed';
  exit();
}


printSfiCalendar($USERNAME, $PASSWORD, $OWNERID, $BASEURL);