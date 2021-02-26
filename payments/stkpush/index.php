<?php

/*This script sends request to subscribers handset asking them to confirm the transaction by entering their MPESA PIN NO*/
include("../../connect/connect.php");
$mobile = sanitize_string($_POST['mobile']);
$amount = sanitize_string($_POST['amount']);
$item_desc =sanitize_string($_POST['package_name']);

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
$url = 'https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest';


  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token)); //setting custom header

$timestamp=date('Ymdhis');
$business_short_code = '332500';
$passkey ="7b3b7bb3982611351c5649958fa8bb9983d3845007e909ac37124067704a16d4";
$password = base64_encode($business_short_code.$passkey.$timestamp);

  $curl_post_data = array(
  //Fill in the request parameters with valid values
  'BusinessShortCode'=>$business_short_code,
  'Password' => $password,
  'Timestamp' => $timestamp,
  'TransactionType' => 'CustomerPayBillOnline',
  'Amount' => $amount,
  'PartyA' => $mobile,
  'PartyB' => '332500', 
'PhoneNumber' => $mobile,
  'CallBackURL' => 'https://portal.fon.co.ke/payments/stkpush/callback/',
  'AccountReference' => $item_desc,
  'TransactionDesc' => $item_desc
);


  $data_string = json_encode($curl_post_data);

  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

  $curl_response = curl_exec($curl);
/*file_put_contents('log.txt',$curl_response,FILE_APPEND | LOCK_EX);
file_put_contents('log.txt', "\n", FILE_APPEND);*/

echo "<div class='alert alert-success'>Success! A payment confirmation has been sent to <strong>$mobile</strong>. Kindly check your phone to confirm payment request. </div>";
?>
