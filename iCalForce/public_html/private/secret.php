<?php
// $USERNAME - variable that contains your Salesforce.com username (must be in the form of an email)
// $PASSWORD - variable that contains your Salesforce.com password

$USERNAME = $_SERVER['USERNAME'];
$PASSWORD = $_SERVER['PASSWORD'];

$OWNERID  = '123456789012345';
if (isset($_SERVER['OWNERID'])) {
  $OWNERID  = $_SERVER['OWNERID'];
}

$BASEURL = 'https://ap.salesforce.com';
if (isset($_SERVER['BASEURL'])) {
  $BASEURL  = $_SERVER['BASEURL'];
}


require_once ('../../config/whitelist.php');
require_once ('../../vendor/icalforce/icalendar.inc.php');


$showDetail = false;

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

      if (isset($_GET['m']) && $_GET['m'] == 1 && isset($rec['allow-detail']) && $rec['allow-detail'] == true) {
        $showDetail = true;
      }
      break;
    }
  }
  if (! isset($OWNERID)) {
    header('HTTP/1.1 403 Forbidden');
    echo 'bad pub token';
    exit();
  }
} else {
  if (isset($_GET['u'])) {
    $OWNERID = $_GET['u'];
  }
  
  if (! preg_match('/^[A-Za-z0-9]{15}$/', $OWNERID)) {
    header('HTTP/1.1 403 Forbidden');
    echo 'bad id';
    exit();
  }
  
  // checking whitelist.
  if (isset($ICALFORCEWHITELIST_19b70db3_f172_40eb_910c_f356365166c1)) {
    if (! array_key_exists($OWNERID, $ICALFORCEWHITELIST_19b70db3_f172_40eb_910c_f356365166c1)) {
      header('HTTP/1.1 403 Forbidden');
      echo 'disallowed';
      exit();
    }
    
    $rec = $ICALFORCEWHITELIST_19b70db3_f172_40eb_910c_f356365166c1[$OWNERID];

    if (isset($_GET['m']) && $_GET['m'] == 1 && isset($rec['allow-detail']) && $rec['allow-detail'] == true) {
      $showDetail = true;
    }
  }
}


printSfiCalendar($USERNAME, $PASSWORD, $OWNERID, $BASEURL, $showDetail);