
<?php

$mobile_no= sanitize_string($_POST['mobile']);
$qty= sanitize_string($_POST['qty']);
$amount= sanitize_string($_POST['amount']);

date_default_timezone_set("Africa/Nairobi");
$consumerkey="EQAUyxMGWhz25YKrytCAbHCPvv8cK5R9";
$consumersecret="GxRFZU5nN8IWCiU9";

$authenticationurl='https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials';
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
  $url = 'https://sandbox.safaricom.co.ke/mpesa/stkpushquery/v1/query';

  $curl = curl_init();
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization:Bearer '.$access_token)); //setting custom header
  
  /***Query the DB for the Checkout request id that was initialy saved in DB***/
 $checkout_request_id = 'ws_CO_DMZ_536443248_08072019154517163';
$business_short_code = '174379';
$timestamp=date('Ymdhis');
$business_short_code = '174379';
$passkey ='bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919';
$password = base64_encode($business_short_code.$passkey.$timestamp);
  $curl_post_data = array(
  //Fill in the request parameters with valid values
  'BusinessShortCode' => $business_short_code,
  'Password' => $password,
  'Timestamp' => $timestamp,
  'CheckoutRequestID' => $checkout_request_id
);

  $data_string = json_encode($curl_post_data);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);

$curl_response = curl_exec($curl);
file_put_contents('log.txt',$curl_response,FILE_APPEND | LOCK_EX);
file_put_contents('log.txt', "\n", FILE_APPEND);

echo $curl_response;

//Put the json string that we received from Safaricom to an array
$array = json_decode($curl_response, true);
$request_id = $array['MerchantRequestID'];
$checkout=$array['CheckoutRequestID'];
$response_code = $array['ResponseCode'];
$result_desc = $array['ResultDesc'];
$response_desc = $array['ResponseDescription'];
$response_code = $array['ResultCode'];

/***You can save this record into DB or update the existing record**/

?>
