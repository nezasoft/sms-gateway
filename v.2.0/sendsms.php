<?php
session_start();
date_default_timezone_set("Africa/Nairobi");
$token = $_SESSION['SMS_TOKEN'];
 $timestamp=date('Ymdhis');
 $uniqid = rand();
 
 //lets try to send a test sms
 $data = '{
"timeStamp": "'.$timestamp.'",
"dataSet": [
{
"userName": "6dcpuser",
"channel": "sms",
"packageId": "4616",
"oa": "SDPTest",
"msisdn": "254758934575,254795421629",
"message": "hello testing online promo nov 1",
"uniqueId": "'.$uniqid.'",
"actionResponseURL": "http://41.79.8.123/gateway/v.2.0/message/"
}
]
}';    
  $post_data = json_encode($data);  
  $ch = curl_init('https://dtsvc.safaricom.com:8480/api/public/CMS/bulksms/');
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
      'X-Authorization: Bearer '.$token,
      'Content-Type: application/json')
  );
 
  $result = curl_exec($ch);
  if(curl_errno($ch)) {
    $error_msg = curl_error($ch);
  }

  if(isset($error_msg)) {
    // TODO - Handle cURL error accordingly
    echo $error_msg;
} 
print_r(json_decode($result));
curl_close($ch);

?> 
