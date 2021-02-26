

<?php
session_start();
date_default_timezone_set("Africa/Nairobi");
ini_set("display_errors",true);
header('Content-Type: application/json');
 // A sample PHP Script to POST data using cURL  
  $data = array('password' => '#EDC4rfv','username' => 'FrontierOpticals _apiuser');    
  $post_data = json_encode($data);    
  // Prepare new cURL resource
  $ch = curl_init('https://dtsvc.safaricom.com:8480/api/auth/login');
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLINFO_HEADER_OUT, true);
  curl_setopt($ch, CURLOPT_POST, true);
  curl_setopt($ch, CURLOPT_FAILONERROR, true);//catch any errors
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);//Disable SSSL
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
    
  // Set HTTP Header for POST request 
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Accept: application/json',
      'X-Requested-With: XMLHttpRequest',
      'X-Authorization: Bearer',
      'Content-Type: application/json')
  );
//  print_r($ch);
 // exit();
    
  // Submit the POST request
  $result = curl_exec($ch);
  if(curl_errno($ch)) {
    $error_msg = curl_error($ch);
  }

if (isset($error_msg)) {
    // TODO - Handle cURL error accordingly
    echo $error_msg;
}
 print_r(json_decode($result));    
  // ClosecURL session handle
  curl_close($ch);
  
  //lets parse the json response and read the values
  $resp = json_decode($result, true);
  $message = $resp['msg'];
  $token = $resp['token'];
  $refresh_token = $resp['refreshToken'];
  //save token in session
  
  $_SESSION['SMS_TOKEN']=$token;
  
 // echo $message."--".$token."--".$refresh_token;
//wait for 10  seconds then send another request
sleep(10);
 
?>





