<?php
// SOAP_CLIENT_BASEDIR - folder that contains the PHP Toolkit and your WSDL
// $USERNAME - variable that contains your Salesforce.com username (must be in the form of an email)
// $PASSWORD - variable that contains your Salesforce.com password

$USERNAME = $_SERVER['USERNAME'];
$PASSWORD = $_SERVER['PASSWORD'];
$OWNERID  = $_SERVER['OWNERID'];

$BASEURL = 'https://ap1.salesforce.com';
if (isset($_SERVER['BASEURL'])) {
  $BASEURL  = $_SERVER['BASEURL'];
}


define('SOAP_CLIENT_BASEDIR', '../soapclient.repo/soapclient');
require_once (SOAP_CLIENT_BASEDIR.'/SforceEnterpriseClient.php');
require_once ('../icalforce/whitelist.php');


if (isset($_GET['u'])) {
  $OWNERID = $_GET['u'];
  if (! preg_match('/^[A-Za-z0-9]{15}$/', $OWNERID)) {
    throw new Exception('bad id');
  }
}
if (isset($ICALFORCEWHITELIST_19b70db3_f172_40eb_910c_f356365166c1)) {
  if (! array_key_exists($OWNERID, $ICALFORCEWHITELIST_19b70db3_f172_40eb_910c_f356365166c1)) {
    throw new Exception('bad id');
  }
}
  

function printSfiCalendar($userName, $pass, $ownerId, $baseUrl) {
  function guid($str){
    $charid = strtoupper(md5($str));
    $hyphen = chr(45); // "-"
    $uuid = ""
      .substr($charid, 0, 8).$hyphen
      .substr($charid, 8, 4).$hyphen
      .substr($charid,12, 4).$hyphen
      .substr($charid,16, 4).$hyphen
      .substr($charid,20,12)
      ;
    return $uuid;
  }
  
  function get_timezone_offset($remote_tz, $origin_tz = null) {
    if($origin_tz === null) {
      if(!is_string($origin_tz = date_default_timezone_get())) {
        return false; // A UTC timestamp was returned -- bail out!
      }
    }
    $origin_dtz = new DateTimeZone($origin_tz);
    $remote_dtz = new DateTimeZone($remote_tz);
    $origin_dt = new DateTime("now", $origin_dtz);
    $remote_dt = new DateTime("now", $remote_dtz);
    $offset = $origin_dtz->getOffset($origin_dt) - $remote_dtz->getOffset($remote_dt);
    return $offset;
  }

  try {
    $calGuid = guid($userName . $ownerId);
    
    $mySforceConnection = new SforceEnterpriseClient();
    $mySoapClient = $mySforceConnection->createConnection(SOAP_CLIENT_BASEDIR.'/enterprise.wsdl.xml');
    $mylogin = $mySforceConnection->login($userName, $pass);
    
    $nowDate = new DateTime();
    $startDate = clone $nowDate;
    $startDate = $startDate->sub(new DateInterval('P366D'));
    $endDate = clone $nowDate;
    $endDate = $endDate->add(new DateInterval('P400D'));
    
    $query = 'SELECT Id, Name, TimeZoneSidKey from User where Id = \'' . $ownerId . '\'';
    $users = $mySforceConnection->query(($query));
    
    $query = '
SELECT
   Id
 , Subject
 , ActivityDateTime
 , StartDateTime
 , EndDateTime
 , Location
 , IsAllDayEvent
 , OwnerId
from Event
where
      OwnerId = \'' . $ownerId . '\'
  and StartDateTime >= ' . gmdate('Y-m-d\TH:i:s\Z', $startDate->getTimestamp()) . '
  and StartDateTime <  ' . gmdate('Y-m-d\TH:i:s\Z', $endDate->getTimestamp()) . '
order by StartDateTime limit 10000';
    
    $response = $mySforceConnection->query(($query));
    
    header("Cache-Control: no-cache");
    header('Content-type: text/plain; charset=utf-8');
    //header('Content-Disposition: attachment; filename="' . $calGuid . '.ics"');
    
    $tzoffset = get_timezone_offset('UTC', $users->records[0]->TimeZoneSidKey);
    $tzoffset = ((int)($tzoffset / 3600)) * 100 + ((int)($tzoffset / 60)) % 60;
    
    echo "BEGIN:VCALENDAR\r\n",
         "PRODID:My Cal\r\n",
         "VERSION:2.0\r\n",
         "METHOD:PUBLISH\r\n",
         "CALSCALE:GREGORIAN\r\n",
         "BEGIN:VTIMEZONE\r\n",
         "TZID:", $users->records[0]->TimeZoneSidKey, "\r\n",
         "BEGIN:STANDARD\r\n",
         "DTSTART:19700101T000000Z\r\n",
         "TZOFFSETFROM:", sprintf('%1$+05d', $tzoffset), "\r\n",
         "TZOFFSETTO:", sprintf('%1$+05d', $tzoffset), "\r\n",
         "END:STANDARD\r\n",
         "END:VTIMEZONE\r\n",
         "X-WR-CALNAME:", $users->records[0]->Name,"'s calendar\r\n",
         "X-WR-CALDESC:Celebrations of various revolutionary activities.\r\n",
         "X-WR-RELCALID:", $calGuid, "\r\n",
         "X-WR-TIMEZONE:Asia/Tokyo\r\n";
    
    foreach ($response->records as $record) {
      $dateFmt = 'Ymd\THis\Z';
      $timeAdd = 0;
      if ($record->IsAllDayEvent) {
        $dateFmt = 'Ymd';
        $timeAdd = 3600 * 24;
      }
      echo "BEGIN:VEVENT\r\n",
           "UID:mycal/", $calGuid, "/", $record->Id, "\r\n",
           (!$record->IsAllDayEvent ?
               "DTSTAMP:". gmdate('Ymd\THis\Z', strtotime($record->StartDateTime)) . "\r\n" :
               ''),
           "DTSTART:", gmdate($dateFmt, strtotime($record->StartDateTime)), "\r\n",
           "DTEND:"  , gmdate($dateFmt, strtotime($record->EndDateTime) + $timeAdd), "\r\n",
           "SUMMARY:", $record->Subject, "\r\n",
           "DESCRIPTION:", $baseUrl, "/", $record->Id, "\r\n",
           "LOCATION:", (isset($record->Location) ? $record->Location : ''), "\r\n",
           "END:VEVENT\r\n";
    }
    
    echo "END:VCALENDAR\r\n";
  } catch (Exception $e) {
    echo $e;
  }
}

printSfiCalendar($USERNAME, $PASSWORD, $OWNERID, $BASEURL);