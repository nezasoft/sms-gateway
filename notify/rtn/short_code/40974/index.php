<?php
/* Walter Omedo - Frontier Optical Networks Limited -- This application is used to receive SMS from subscribers and give a response
 * 24th Jan 2019 
 */
 
/*This script will handle all incoming messages from mobile subscribers
Mobile users will send to client's short codes and they will be sent to this page by Safaricoms SDP for processing
After receiving the request this script will save the sent message in the DB and  a notifySmsReceptionResponse will be sent back to the SDP to confirm the message has been received
This script will send a confirmation message/details of what the mobile subscriber was requesting for the SDP so that the SDP can deliver to the subscribers handset
The SDP will send a delivery notification back to the fonsms gateway that the message was delivered to the mobile subscribers handset
*/
date_default_timezone_set("Africa/Nairobi");
ini_set("soap.wsdl_cache_enabled","0");
//set_time_limit(0);
//Capture the post request from the SDP
$response = trim(file_get_contents("php://input"));
 //Now lets send a notification that we have received the incoming message from the client
echo '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:loc="http://www.csapi.org/schema/parlayx/sms/notification/v2_2/local">
      	<soapenv:Header/>
      	<soapenv:Body>
      		<loc:notifySmsReceptionResponse/>
      	</soapenv:Body>
      </soapenv:Envelope>';

// sleep for 10 seconds
sleep(10);

/*
file_put_contents('log.txt',$response,FILE_APPEND | LOCK_EX);
file_put_contents('log.txt', "\n", FILE_APPEND);*/

 //converting
$response = str_replace("<soap:Body>","",$response);
$response = str_replace("</soap:Body>","",$response);
//Convert to XML
$response1 = simplexml_load_string($response);
//Connect to Database
include("../../../../connect/connect.php");

try{
$conn->beginTransaction();
//Before we do anything lets get current password for the short code service to the SDP
$get_sdp_pass = $conn->prepare("SELECT sys_password FROM sys_settings WHERE section_type='SC' LIMIT 1");
$get_sdp_pass->execute();
$get_sdp_pass_row = $get_sdp_pass->fetch(PDO::FETCH_ASSOC);
$sdp_password = $get_sdp_pass_row['sys_password'];
//Grab the SOAP Header response
$item_headers = $response1->children('soapenv', true)->Header->children('ns1', true)->NotifySOAPHeader; 
foreach($item_headers as $item_header){
$linkid = $item_header->linkid;
$service_id = $item_header->serviceId;
$trace_uniq_id = $item_header->traceUniqueID;

//Get Short code details
$query = $conn->prepare("SELECT sc.id AS short_code_id,sc.short_code,csc.keyword, csc.company_id FROM short_codes AS sc 
LEFT JOIN company_short_codes AS csc ON csc.short_code_id = sc.id WHERE sc.service_id='".$service_id."' LIMIT 1");
$query->execute();
$row = $query->fetch(PDO::FETCH_ASSOC);
$short_code_id = $row['short_code_id'];
$short_code= $row['short_code'];
$company_id = $row['company_id'];
$SPID = '601957';//Service provider ID
$real_pass = $sdp_password;//Service provider password
$serviceId = $service_id;//Service ID
$timestamp= date("YmdHis");
$password = md5($SPID.$real_pass.$timestamp);
$criteria =$row['keyword'];
$endpoint = 'http://192.168.20.230/gateway/notify/sendsms/short_code/';

 //Confirm if this message already exists
 $confirm=$conn->prepare("SELECT id AS message_id FROM incoming_messages WHERE traceuniqid='".$trace_uniq_id."' AND linkid='".$linkid."' AND service_id='".$service_id."' LIMIT 1");
 $confirm->execute();
 $confirm_count = $confirm->rowCount();
 if($confirm_count==0){
 //Save these record into incoming messages 
 $save = $conn->prepare("INSERT INTO  incoming_messages(service_id,short_code_id,traceuniqid,linkid,company_id,msg_date,msg_read,msg_time)
VALUES('".$service_id."','".$short_code_id."', '".$trace_uniq_id."','".$linkid."','".$company_id."',curdate(),'N',curtime())");

 $save->execute();
 }

}

//Lets Grab the items in the XML response
$doc = new DOMDocument();
$doc->loadXML($response);
$correlator = $doc->getElementsByTagName('correlator')->item(0)->nodeValue;
$message = $doc->getElementsByTagName('message')->item(1)->nodeValue;
$sender=substr($doc->getElementsByTagName('senderAddress')->item(0)->nodeValue,4);//Remove "tel:" from the sender address
$activation_no = $doc->getElementsByTagName('smsServiceActivationNumber')->item(0)->nodeValue;
$date_time = $doc->getElementsByTagName('dateTime')->item(0)->nodeValue;
$sender = "254".$sender; //Lets add the +254 Prefix to the mobile no

//Save Log
file_put_contents('log.txt',$correlator."--".$sender."--".$activation_no."--".date('Y-m-d h:i:sa'),FILE_APPEND | LOCK_EX);
file_put_contents('log.txt', "\n", FILE_APPEND);

//Update incoming messages details
$update_2 = $conn->prepare("UPDATE incoming_messages SET message='".str_replace("'","",$message)."', mobile_no='".$sender."',keyword='".$activation_no."' WHERE linkid='".$linkid."' AND company_id='".$company_id."' AND service_id='".$service_id."' LIMIT 1");
$update_2->execute();


 //We will send the mobile subscriber a custom message to notify them we have recieved thier request
 //Alternatively we can query the Db and send them the content they are requesting for
 //So in our case since they are looking for internet services we will send them the products and pricing
  $get_data = $conn->prepare("SELECT response_message,company_name,sender_name FROM company_short_codes AS cs LEFT JOIN company AS c ON c.id = cs.company_id WHERE c.id='".$company_id."' AND cs.short_code_id='".$short_code_id."' LIMIT 1");
  $get_data->execute();
  $get_data_row = $get_data->fetch(PDO::FETCH_ASSOC);
  $response_message = $get_data_row['response_message'];
  /*Change this code */
  $sender_name = $short_code;
  //$sender_name ='6127';
  /*Remove this section */
  /*$request_identifier = '7856451545445754575'; 
  $insert_outgoing = $conn->prepare("INSERT INTO outgoing_messages(message,short_code_id,company_id,delivery_status,date_sent,time_sent,user_id,response_code,mobile_no)
VALUES('".$response_message."','".$short_code_id."','".$company_id."','Pending',curdate(),curtime(),'2','".$request_identifier."','".$sender."')");
$insert_outgoing->execute();

   /*Remove this section */
 //Lets Generate the Correlator for this request
   function generateRandomString($length = 15) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
   }
   $correlator = 'FON'.generateRandomString(); 
 //Lets invoke the sendSMS Method on the SDP Platform and to deliver the message to the subscriber/'s handset
 $data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v2="http://www.huawei.com.cn/schema/common/v2_1"
	xmlns:loc="http://www.csapi.org/schema/parlayx/sms/send/v2_2/local">
	<soapenv:Header>
		<v2:RequestSOAPHeader>
			<v2:spId>'.$SPID.'</v2:spId>
			<v2:spPassword>'.$password.'</v2:spPassword>
			<v2:serviceId>'.$serviceId.'</v2:serviceId>
			<v2:timeStamp>'.$timestamp.'</v2:timeStamp>
			<!--mandatory if service is on-demand-->
			<v2:linkid>'.$linkid.'</v2:linkid>
		</v2:RequestSOAPHeader>
	</soapenv:Header>
	<soapenv:Body>
		<loc:sendSms>
			<!--1 or more repetitions:-->
			<loc:addresses>tel:'.$sender.'</loc:addresses>
			<!--Optional:-->
			<loc:senderName>'.$sender_name.'</loc:senderName>
			<loc:message>'.$response_message.'</loc:message>
			<!--Optional:-->
			<loc:receiptRequest>
				<endpoint>'.$endpoint.'</endpoint>
				<interfaceName>SmsNotification</interfaceName>
				<correlator>'.$correlator.'</correlator>
			</loc:receiptRequest>
		</loc:sendSms>
	</soapenv:Body>
</soapenv:Envelope>';
//sleep for 10 seconds before responding again to the SDP
sleep(10);
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://10.66.49.147:8310/SendSmsService/services/SendSms");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml; charset=utf-8", "Content-Length: " . strlen($data)));
$response = curl_exec($ch); 
curl_close($ch);
//converting
$response = str_replace("<soap:Body>","",$response);
$response = str_replace("</soap:Body>","",$response);
//Log Response
$log=$conn->prepare("INSERT INTO logs(log_text,log_date,log_time)VALUES('".$response."',curdate(),curtime())");
$log->execute();
//Loads the XML
$xml = simplexml_load_string($response);   
// Grabs the items in the response 
$items = $xml->children('soapenv', true)->Body->children('ns1', true)->sendSmsResponse; 
// Take the response and generate some mark up              
foreach($items as $item){
$request_identifier = $item->result;
//Insert into outgoing messages
$insert_outgoing = $conn->prepare("INSERT INTO outgoing_messages(sms_count,message,short_code_id,company_id,delivery_status,date_sent,time_sent,user_id,response_code,mobile_no,correlator)
VALUES(1,'".$response_message."','".$short_code_id."','".$company_id."','Pending',curdate(),curtime(),'2','".$request_identifier."','".$sender."','".$correlator."')");
$insert_outgoing->execute();
               
}

//Confirm contact
$confirm = $conn->prepare("SELECT id FROM contacts WHERE company_id='".$company_id."' AND mobile_no='".$sender."' LIMIT 1");
$confirm->execute();
$confirm_count = $confirm->rowCount();

if($confirm_count==0){
//Lets save this contact
$save_contact = $conn->prepare("INSERT INTO contacts(mobile_no,contact_name,company_id,date_created,user_id,account_no)
VALUES('".$sender."','Anonymous','".$company_id."',curdate(),'2','N/A')");
$save_contact->execute();
}

$conn->commit();
}catch(PDOException $e){
$conn->rollBack();
file_put_contents('log.txt',$e->getMessage(),FILE_APPEND | LOCK_EX);
file_put_contents('log.txt', "\n", FILE_APPEND);
}

?>

