<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- This application is used to send SMS to use groups
 * 17th July 2019
 */

 //Connect to DB
 include("../connect/connect.php"); 
date_default_timezone_set("Africa/Nairobi");
ini_set("display_errors",true);

//check if its an ajax request
 if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
$group = sanitize_string($_POST['group']);
$message = str_replace("'",'',sanitize_string($_POST['message_group']));
  /* $message ="This is a test message";
   $contacts='254724802834';*/
   
//Before we do anything lets get current password for the short code service to the SDP
$get_sdp_pass = $conn->prepare("SELECT sys_password FROM sys_settings WHERE section_type='BK' LIMIT 1");
$get_sdp_pass->execute();
$get_sdp_pass_row = $get_sdp_pass->fetch(PDO::FETCH_ASSOC);
$sdp_password = $get_sdp_pass_row['sys_password'];

   $SPID = '601958';//Service provider ID
   $real_pass = $sdp_password;//Service provider password
   $timestamp= date("YmdHis");
   $password = md5($SPID.$real_pass.$timestamp);
   $endpoint = 'http://192.168.20.230/gateway/notify/sendsms/';//Callback URL   
  // $contacts = explode(',',$contacts);
   $count = 0;


   
   //Get group contacts
   $contacts = $conn->prepare("SELECT mobile_no FROM group_contacts AS gc LEFT JOIN contacts AS c ON c.id = gc.contact_id WHERE gc.group_id='".$group."' ");
   $contacts->execute();
   $contact_rows = $contacts->fetchAll(PDO::FETCH_ASSOC);
   foreach($contact_rows as $contact){
   	$count++;
	
   }

   
  try{
  $conn->beginTransaction();  
  //Lets get the short code to send the bulk sms
  $query_short_code = $conn->prepare("SELECT sc.id AS short_code_id,sc.short_code,csc.sender_name FROM company_short_codes AS csc  
  LEFT JOIN short_codes AS sc ON sc.id=csc.short_code_id WHERE sc.product_id=2 AND csc.company_id='".$_SESSION['FON_G_COMPANY_ID']."'");
 $query_short_code->execute();
 $short_code_row = $query_short_code->fetch(PDO::FETCH_ASSOC);
 $short_code = $short_code_row['short_code'];
 $sender_name = $short_code_row['short_code'];
 //Lets generate a unique reference no and use it as a correlator
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

   //Confirm if this record is in db and if its active
   $confirm = $conn->prepare("SELECT sc.id AS short_code_id,sc.short_code,sc.correlator,sc.service_id,c.company_name, csc.id AS company_id FROM short_codes AS sc 
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
   $short_code_id = $query_row['short_code_id'];
   $short_code = $query_row['short_code'];
   
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
			<!--1 or more repetitions:-->';
			foreach($contact_rows as $mobile_no){
			$data .='<loc:addresses>tel:'.$mobile_no.'</loc:addresses>';
			}
						
			$data.='<!--Optional:-->
			<loc:senderName>'.$sender_name.'</loc:senderName>
			<loc:message>'.$message.'</loc:message>
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

//Lets save this message to our DB
   $query = $conn->prepare("INSERT INTO outgoing_messages(sms_count,message,short_code_id,mobile_no,company_id,date_sent,time_sent,user_id,delivery_status,correlator)
   VALUES('".$count."','".$message."','".$short_code_id."','".$short_code."','".$_SESSION['FON_G_COMPANY_ID']."',curdate(),curtime(),'".$_SESSION['FON_G_USER_ID']."','Pending','".$correlator."')");
    $query->execute();
   //Lets use mysql last_insert_id() to get the message id
   $query_last_row = $conn->prepare("SELECT MAX(id) as last_id FROM outgoing_messages WHERE company_id='".$_SESSION['FON_G_COMPANY_ID']."' AND user_id='".$_SESSION['FON_G_USER_ID']."'");
   $query_last_row->execute();
   $last_row = $query_last_row->fetch(PDO::FETCH_ASSOC);
   $message_id = $last_row['last_id'];
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

$conn->commit();	
//echo '<script>$("#form_section").empty();</script>';
echo "<br/><div class='alert alert-success'><i class='fa fa-check-circle' ></i> <strong>".$count."</strong> Message(s) Sent!</div>";
/*echo '<script>
		 window.setTimeout(function(){
		// Move to a new location or you can do something else
		window.location.href = "?settings&action=list_contacts";
		}, 3000);
	  </script>'; */  
   }
    
}catch(PDOException $e){
$conn->rollBack();
die("<div class='alert alert-danger'>Database Error:- ".$e->getMessage()."</div>");
}
}else{
  die("<font color='red'>You are not authorized to access this content.</font>");
}	
?>

