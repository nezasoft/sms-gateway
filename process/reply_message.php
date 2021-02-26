<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Reply Message
 * 30th Dec 2018
 */

include("../connect/connect.php");
ini_set("display_errors",true);

  $SPID = '601958';//Service provider ID
  $real_pass = '#Fon789S';//Service provider password
  $timestamp= date("YmdHis");
  $password = md5($SPID.$real_pass.$timestamp);
  $endpoint = 'http://192.168.20.230/gateway/notify/sendsms/';
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
	$reply_message = $_POST['reply_message'];
	$message_id = sanitize_string($_POST['message_id']);
	$mobile_no = sanitize_string($_POST['mobile_no']);
	$short_code = sanitize_string($_POST['short_code']);
     
try{
$conn->beginTransaction();
//Check if record exist
$confirm = $conn->prepare("SELECT id AS reply_id FROM message_replies WHERE message_id='".$message_id."' AND user_id='".$_SESSION['FON_G_USER_ID']."' LIMIT 1 ");
$confirm->execute();
$count_confirm = $confirm->rowCount();

if($count_confirm>=1){
die("<div class='alert alert-danger'>* You have already responded to this message!</div>");
}

//Lets save this record
$save = $conn->prepare("INSERT INTO message_replies(message_id, reply_message,user_id,reply_date,reply_time)
VALUES('".$message_id."','".$reply_message."','".$_SESSION['FON_G_USER_ID']."',curdate(),curtime())");
$save->execute();
$row_count = $save->rowCount();

if($row_count>=1){
	//Send request to the Safariroms SDP Platform to deliver the text message to the mobile subscribers handset
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
   
   //Confirm if this record is in db
   $confirm = $conn->prepare("SELECT sc.id AS short_code_id,sc.short_code,sc.correlator,sc.service_id,c.company_name,csc.sender_name, csc.id AS company_id FROM short_codes AS sc 
   LEFT JOIN company_short_codes AS csc ON csc.short_code_id = sc.id LEFT JOIN company AS c ON c.id = csc.company_id 
   WHERE sc.short_code='".$short_code."' AND sc.product_id ='2' AND csc.company_id='".$_SESSION['FON_G_COMPANY_ID']."' AND sc.status='1'  LIMIT 1");
   $confirm->execute();
   $check_num_rows = $confirm->rowCount();
   $query_row = $confirm->fetch(PDO::FETCH_ASSOC);

   if($check_num_rows==0){
     die("<div class='alert alert-danger'>Sorry, you cannot use this service right now. It has been deactivated.</div>");
   }else{
   //Lets get the short code details
   $service_id = $query_row['service_id'];
   //$correlator = $query_row['correlator'];
   $sender_name = $query_row['sender_name'];
   $short_code_id = $query_row['short_code_id'];
   $short_code = $query_row['short_code'];
   
   //Lets save this message to our DB
   $query = $conn->prepare("INSERT INTO outgoing_messages(sms_count,message,short_code_id,mobile_no,company_id,date_sent,time_sent,user_id,delivery_status)
   VALUES(1,'".$reply_message."','".$short_code_id."','".$mobile_no."','".$_SESSION['FON_G_COMPANY_ID']."',curdate(),curtime(),'".$_SESSION['FON_G_USER_ID']."','Pending')");
    $query->execute();
   //Lets use mysql last_insert_id() to get the message id
   $query_last_row = $conn->prepare("SELECT MAX(id) as last_id FROM outgoing_messages WHERE company_id='".$_SESSION['FON_G_COMPANY_ID']."' AND user_id='".$_SESSION['FON_G_USER_ID']."'");
   $query_last_row->execute();
   $last_row = $query_last_row->fetch(PDO::FETCH_ASSOC);
   $message_id = $last_row['last_id'];
   
   $data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v2="http://www.huawei.com.cn/schema/common/v2_1"
	xmlns:loc="http://www.csapi.org/schema/parlayx/sms/send/v2_2/local">
	<soapenv:Header>
		<v2:RequestSOAPHeader>
			<v2:spId>'.$SPID.'</v2:spId>
			<v2:spPassword>'.$password.'</v2:spPassword>
			<v2:serviceId>'.$service_id.'</v2:serviceId>
			<v2:timeStamp>'.$timestamp.'</v2:timeStamp>
		</v2:RequestSOAPHeader>
	</soapenv:Header>
	<soapenv:Body>
		<loc:sendSms>
			<!--1 or more repetitions:-->
			<loc:addresses>tel:'.$mobile_no.'</loc:addresses>			
			<!--Optional:-->
			<loc:senderName>'.$sender_name.'</loc:senderName>
			<loc:message>'.$message_reply.'--'.titleCase($_SESSION['FON_G_FNAME']).'</loc:message>
			<!--Optional:-->
			<loc:receiptRequest>
				<endpoint>'.$endpoint.'</endpoint>
				<interfaceName>SmsNotification</interfaceName>
				<correlator>'.$correlator.'</correlator>
			</loc:receiptRequest>
		</loc:sendSms>
	</soapenv:Body>
</soapenv:Envelope>';
 
 
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
//Loads the XML
$xml = simplexml_load_string($response);  
//Grabs the items in the response 
$items = $xml->children('soapenv', true)->Body->children('ns1', true)->sendSmsResponse; 
//Take the response and generate some mark up
foreach($items as $item){
$request_identifier = $item->result;
//Update outgoing message status
$update_outgoing = $conn->prepare("UPDATE outgoing_messages SET response_code='".$request_identifier."' WHERE id='".$message_id."' LIMIT 1");
$update_outgoing->execute();

}
/*
//After sending the sms lets send a delivery request to confirm the status of these messages
//To do so we will invoke getSmsDeliveryStatusRequest
$data = '<soapenv:Envelope
	xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
	xmlns:v2="http://www.huawei.com.cn/schema/common/v2_1"
	xmlns:loc="http://www.csapi.org/schema/parlayx/sms/send/v2_2/local">
	<soapenv:Header>
		<v2:RequestSOAPHeader>
			<v2:spId>'.$SPID.'</v2:spId>
			<v2:spPassword>'.$password.'</v2:spPassword>
			<v2:serviceId>'.$service_id.'</v2:serviceId>
			<v2:timeStamp>'.$timestamp.'</v2:timeStamp>
		</v2:RequestSOAPHeader>
	</soapenv:Header>
	<soapenv:Body>
<loc:getSmsDeliveryStatus>
	<loc:requestIdentifier>'.$request_identifier.'</loc:requestIdentifier>
</loc:getSmsDeliveryStatus>
	</soapenv:Body>
</soapenv:Envelope>';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://10.66.49.198:8310/SendSmsService/services/SendSms");
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
//Loads the XML
$xml = simplexml_load_string($response);  
//Grabs the items in the response 
$items = $xml->children('soapenv', true)->Body->children('ns1', true)->getSmsDeliveryStatusResponse; 
//Take the response and generate some mark up
foreach($items as $item){
$mobile_no = substr($item->result->address,4);
$status = $item->result->deliveryStatus;
//Update outgoing message status
$update_outgoing_status = $conn->prepare("INSERT INTO delivery_status(message_id,company_id,mobile_no,delivery_status)VALUES('".$message_id."','".$_SESSION['FON_SMS_COMPANY_ID']."','".$mobile_no."','".$status."')");
$update_outgoing_status->execute();

//Update outgoing message status
$update_outgoing = $conn->prepare("UPDATE outgoing_messages SET delivery_status='".$status."' WHERE id='".$message_id."' LIMIT 1");
$update_outgoing->execute();


}*/
   
   }
   
   $conn->commit();
	
echo '<script>$(".modal-content").empty();</script>';
echo "<div class='alert alert-success'><i class='fa fa-check-circle' ></i> Message Sent!</div>";
echo '<script>
		 window.setTimeout(function(){
		// Move to a new location or you can do something else
		window.location.href = "?settings&action=new_category";
		}, 3000);
	  </script>';
}else{
die("<div class='alert alert-danger'>Error sending message!</div>");
}	  

}catch(PDOException $e){
$conn->rollBack();
 die("<div class='alert alert-danger'>Database Error:- ".$e->getMessage()."</div>");
}

}else{
die("<font color='red'>You are not authorized to access this content.</font>");
}
?>