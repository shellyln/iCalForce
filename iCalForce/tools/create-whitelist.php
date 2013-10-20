<?php
// you can generate "whitelist.php" file as follow:
// > env USERNAME='alice@example.com' PASSWORD='passSecuritytoken' php create-whitelist.php > ./whitelist.php

// SOAP_CLIENT_BASEDIR - folder that contains the PHP Toolkit and your WSDL
// $USERNAME - variable that contains your Salesforce.com username (must be in the form of an email)
// $PASSWORD - variable that contains your Salesforce.com password

$USERNAME = $_SERVER['USERNAME'];
$PASSWORD = $_SERVER['PASSWORD'];

define('SOAP_CLIENT_BASEDIR', '../vendor/soapclient.repo/soapclient');
require_once (SOAP_CLIENT_BASEDIR.'/SforceEnterpriseClient.php');


function guid(){
  mt_srand((double)microtime()*10000); // optional for php 4.2.0 and up.
  $charid = strtoupper(md5(uniqid(rand(), true)));
  $hyphen = '';    //chr(45);  // "-"
  $uuid = ''
      //.chr(123)  // "{"
      .substr($charid, 0, 8).$hyphen
      .substr($charid, 8, 4).$hyphen
      .substr($charid,12, 4).$hyphen
      .substr($charid,16, 4).$hyphen
      .substr($charid,20,12)
      //.chr(125)
      ;            // "}"
  return $uuid;
}


try {
  $mySforceConnection = new SforceEnterpriseClient();
  $mySoapClient = $mySforceConnection->createConnection(SOAP_CLIENT_BASEDIR.'/enterprise.wsdl.xml');
  $mylogin = $mySforceConnection->login($USERNAME, $PASSWORD);
  
  $query = 'SELECT Id, Name, TimeZoneSidKey from User where UseICalForce__c = true';
  $response = $mySforceConnection->query(($query));
  
  echo "<?php\n\n";
  echo "\$ICALFORCEWHITELIST_19b70db3_f172_40eb_910c_f356365166c1 = array(\n";
  foreach ($response->records as $user) {
    echo "  '", substr($user->Id, 0, 15), "' => array('pub-token' => '", guid(), "'),\n";
  }
  echo ");\n\n";
  
} catch (Exception $e) {
  echo $e;
}
