<?php

$consumerkey ="5elewJRXfE6LZDOUbh3M92UJMWJLzQa4";
$consumersecret ="QZtLBDiokaG9w9rH";
$authenticationurl='https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
$credentials= base64_encode($consumerkey.':'.$consumersecret);
$username=$consumerkey ;
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

$url = 'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/simulate';

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token)); //setting custom header


  $curl_post_data = array(
          //Fill in the request parameters with valid values
         'ShortCode' => '600534',
         'CommandID' => 'CustomerPayBillOnline',
         'Amount' => '100',
         'Msisdn' => '254708374149',
         'BillRefNumber' => 'TSTBill123FON'
  );

  $data_string = json_encode($curl_post_data);

  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

  $curl_response = curl_exec($curl);
file_put_contents('log.txt',$curl_response,FILE_APPEND | LOCK_EX);
file_put_contents('log.txt', "\n", FILE_APPEND);

echo $curl_response;
