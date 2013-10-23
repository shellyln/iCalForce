<?php

require_once ('../vendor/PHP-OAuth2/Client.php');
require_once ('../vendor/PHP-OAuth2/GrantType/IGrantType.php');
require_once ('../vendor/PHP-OAuth2/GrantType/AuthorizationCode.php');
require_once ('../vendor/icalforce/oauth.inc.php');

$client = new OAuth2\Client($CLIENT_ID, $CLIENT_SECRET);
$keyInfo = ICalForce\cookieMakeCryptKey();


if (isset($_GET['code'])) {
  $params = array('code' => $_GET['code'], 'redirect_uri' => $REDIRECT_URI);
  $response = $client->getAccessToken(ICalForce\TOKEN_ENDPOINT, 'authorization_code', $params);
  
  $cookieEnc = ICalForce\cookieEncrypt($keyInfo, json_encode($response));
  setcookie('V_04660A06A99FEC845360DA2C6D2557A3', $cookieEnc, 0, '/', $_SERVER['HTTP_HOST'], true);
  
  header('Location: ' . $HOME_URI);
  die('Redirect');
}
elseif (isset($_GET['error'])) {
  echo 'Error. Could not get Auth-Token. Please contact your system administrator.';
}
else {
  $auth_url = $client->getAuthenticationUrl(ICalForce\AUTHORIZATION_ENDPOINT, $REDIRECT_URI);
  header('Location: ' . $auth_url);
  die('Redirect');
}
