<?php
namespace ICalForce;


const AUTHORIZATION_ENDPOINT = 'https://login.salesforce.com/services/oauth2/authorize';
const TOKEN_ENDPOINT         = 'https://login.salesforce.com/services/oauth2/token';


$CLIENT_ID     = $_SERVER['CLIENT_ID'];
$CLIENT_SECRET = $_SERVER['CLIENT_SECRET'];

$REDIRECT_URI = 'https://' . $_SERVER['HTTP_HOST'] . '/oa-callback.php';
$HOME_URI     = 'https://' . $_SERVER['HTTP_HOST'] . '/my.php';


function cookieMakeCryptKey() {
  # the key should be random binary, use scrypt, bcrypt or PBKDF2 to
  # convert a string into a key
  # key is specified using hexadecimal
  $key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
  
  # show key size use either 16, 24 or 32 byte keys for AES-128, 192
  # and 256 respectively
  $key_size =  strlen($key);

  # create a random IV to use with CBC encoding
  $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
  $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
  
  return array('key' => $key, 'key_size' => $key_size, 'iv_size' => $iv_size, 'iv' => $iv);
}


function cookieEncrypt($keyInfo, $plaintext) {
  # creates a cipher text compatible with AES (Rijndael block size = 128)
  # to keep the text confidential 
  # only suitable for encoded input that never ends with value 00h
  # (because of default zero padding)
  $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $keyInfo['key'],
                               $plaintext, MCRYPT_MODE_CBC, $keyInfo['iv']);

  # prepend the IV for it to be available for decryption
  $ciphertext = $keyInfo['iv'] . $ciphertext;
  
  # encode the resulting cipher text so it can be represented by a string
  $ciphertext_base64 = base64_encode($ciphertext);

  return $ciphertext_base64;

  # === WARNING ===

  # Resulting cipher text has no integrity or authenticity added
  # and is not protected against padding oracle attacks.
}


function cookieDecrypt($keyInfo, $ciphertext_base64) {
  # --- DECRYPTION ---
  
  $ciphertext_dec = base64_decode($ciphertext_base64);
  
  # retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
  $iv_dec = substr($ciphertext_dec, 0, $keyInfo['iv_size']);
  
  # retrieves the cipher text (everything except the $iv_size in the front)
  $ciphertext_dec = substr($ciphertext_dec, $keyInfo['iv_size']);

  # may remove 00h valued characters from end of plain text
  $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $keyInfo['key'],
                                  $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);
  
  $plaintext_dec = str_replace("\0", "", $plaintext_dec);
  
  return $plaintext_dec;
}
