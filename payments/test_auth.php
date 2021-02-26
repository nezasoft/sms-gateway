<?php
date_default_timezone_set("Africa/Nairobi");
$consumerkey="CIkCVEslf7kQLX8aXPyZmzv0VpcCnflo";
$consumersecret="uoMa725DJueIOKGv";

$authenticationurl='https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$credentials= base64_encode($consumerkey.':'.$consumersecret);
$username=$consumerkey;
$password=$consumersecret;
  // Request headers
  $headers = array(  
    'Content-Type: application/json; charset=utf-8'
  );
  // Request
  $ch = curl_init($authenticationurl);
  curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
  //curl_setopt($ch, CURLOPT_HEADER, TRUE); // Includes the header in the output
  curl_setopt($ch, CURLOPT_HEADER, FALSE); // excludes the header in the output
  curl_setopt($ch, CURLOPT_USERPWD, $username.":".$password); // HTTP Basic Authentication
  $result = curl_exec($ch);  
  $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
$result = json_decode($result);
$access_token=$result->access_token;

echo $access_token;
?>