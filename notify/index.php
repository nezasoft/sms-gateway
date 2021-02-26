<?php

include("../connect/connect.php");
ini_set("display_errors",true);

$rawData = file_get_contents("php://input");
//Convert to XML
$xml = simplexml_load_string($rawData);
//Grab the SOAP Header response
$item_headers = $xml->children('soapenv', true)->Header->children('ns1', true)->NotifySOAPHeader; 
foreach($item_headers as $item_header){
$linkid = $item_header->linkid;
$service_id = $item_header->serviceId;
$trace_uniq_id = $item_header->traceUniqueID;
//Get Short code details
$row = mysql_fetch_array(mysql_query("SELECT sc.id AS short_code_id, csc.id AS company_id FROM short_codes AS sc LEFT JOIN company_short_codes AS csc ON csc.short_code_id = sc.id WHERE sc.service_id='".$service_id."' LIMIT 1"));
$short_code_id = $row['short_code_id'];
$company_id = $row['company_id'];
//Save these record into incoming messages
mysql_query("INSERT INTO incoming_messages(service_id,short_code_id,traceuniqid,linkid,company_id,msg_date)VALUES('".$service_id."','".$short_code_id."','".$trace_uniq_id."','".$linkid."','".$company_id."',now())");

}
// Grabs the items in the response 
$items = $xml->children('soapenv', true)->Body->children('loc', true)->notifySmsReception;   
// Grabs the items in the response 
foreach($items as $item){
$correlator = $item->correlator;
$message = $item->message->message;
$sender = $item->message->senderAddress;
$activation_no = $item->message->smsServiceActivationNumber;
$date_time = $item->message->dateTime;
//Update incoming messages details
mysql_query("UPDATE incoming_messages SET message='".$message."', mobile_no='".$sender."',keyword='".$activation_no."' WHERE traceuniqid='".$trace_uniq_id."' AND company_id='".$company_id."' AND service_id='".$service_id."' LIMIT 1 ");

}
 
 //Now lets send a notification that we have received the incoming message from the client
$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:loc="http://www.csapi.org/schema/parlayx/sms/notification/v2_2/local">
      	<soapenv:Header/>
      	<soapenv:Body>
      		<loc:notifySmsReceptionResponse/>
      	</soapenv:Body>
      </soapenv:Envelope>';
  echo $data;
 //We will send the mobile subscriber a custom message to notify them we have recieved thier request
 //Alternatively we can query the Db and send them the content they are requesting for
 //So in our case since they are looking for internet services we will send them the products and pricing
 
 $get_data_row = mysql_fetch_array(mysql_fetch_assoc("SELECT response_message FROM company_short_codes WHERE company_id='".$company_id."' AND short_code_id='".$short_code_id."' LIMIT 1"));
 $message = $get_data_row['response_message'];
 //Lets invoke the sendSMS Method on the SDP Platform
 
   
?>