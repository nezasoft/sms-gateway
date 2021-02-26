<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- This application will listen to the callback service from Safaricom's SDP for any subscriptions requests from mobile subscribers
 We will the use this information to update our subscriptions list on our database
 * 25th Jan 2019 
 */
 date_default_timezone_set("Africa/Nairobi");
ini_set("soap.wsdl_cache_enabled","0");
//Capture the post request from the SDP
$response = trim(file_get_contents("php://input"));
//Save response in log file
file_put_contents('log.txt',$response,FILE_APPEND | LOCK_EX);
file_put_contents('log.txt', "\n", FILE_APPEND);

 //converting
$response = str_replace("<soap:Body>","",$response);
$response = str_replace("</soap:Body>","",$response);


$doc = new DOMDocument();
$doc->loadXML($response);
$mobile_no = $doc->getElementsByTagName('ID')->item(0)->nodeValue;
$type = $doc->getElementsByTagName('type')->item(0)->nodeValue;
$product_id = $doc->getElementsByTagName('productID')->item(0)->nodeValue;
$service_id = $doc->getElementsByTagName('serviceID')->item(0)->nodeValue;
$service_list = $doc->getElementsByTagName('serviceList')->item(0)->nodeValue;
$update_type = $doc->getElementsByTagName('updateType')->item(0)->nodeValue;
$update_time = $doc->getElementsByTagName('updateTime')->item(0)->nodeValue;
$update_desc = $doc->getElementsByTagName('updateDesc')->item(0)->nodeValue;
$effective_time = $doc->getElementsByTagName('effectiveTime')->item(0)->nodeValue;
$expiry_time = $doc->getElementsByTagName('expiryTime')->item(0)->nodeValue;
$transaction_id = $doc->getElementsByTagName('extensionInfo')->item(2)->nodeValue;

//Connect to Database
include("../../../../../connect/connect.php");

?>