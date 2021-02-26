<?php
ini_set("display_errors",false);
include("../connect/connect.php");
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- This application will enable uses get all the SMSs sent to the SDP by mobile subscribers.
 /*Please note that the messages retrieved will be new messages sent to the Safaricom's SDP since this method "getReceivedSms" was last invoked.
 /*After its invoked the SDP automatically deletes all old messages
 * 12th Dec 2018
 */
date_default_timezone_set("Africa/Nairobi");
ini_set("display_errors",true);
  $product_id = $_POST['product_id'];
  $service_id = $_POST['service_id'];
  $short_code = $_POST['short_code'];
  if($product_id==1){//Short Code
  $SPID = '601957';//Service provider ID
  //Before we do anything lets get current password for the short code service to the SDP
$get_sdp_pass = $conn->prepare("SELECT sys_password FROM sys_settings WHERE section_type='SC' LIMIT 1");
$get_sdp_pass->execute();
$get_sdp_pass_row = $get_sdp_pass->fetch(PDO::FETCH_ASSOC);
$sdp_password = $get_sdp_pass_row['sys_password'];  

  }elseif($product_id==2){//Bulk SMS
  $SPID = '601958';//Service provider ID
 //Before we do anything lets get current password for the short code service to the SDP
$get_sdp_pass = $conn->prepare("SELECT sys_password FROM sys_settings WHERE section_type='BK' LIMIT 1");
$get_sdp_pass->execute();
$get_sdp_pass_row = $get_sdp_pass->fetch(PDO::FETCH_ASSOC);
$sdp_password = $get_sdp_pass_row['sys_password'];
  }
  

$real_pass = $sdp_password;//Service provider password
  $timestamp= date("YdmHis");
  $password = md5($SPID.$real_pass.$timestamp);
  
//Get Short code details
$query = $conn->prepare("SELECT sc.id AS short_code_id,sc.short_code,csc.keyword, csc.company_id FROM short_codes AS sc 
LEFT JOIN company_short_codes AS csc ON csc.short_code_id = sc.id WHERE sc.service_id='".$service_id."' LIMIT 1");
$query->execute();
$row = $query->fetch(PDO::FETCH_ASSOC);
$short_code_id = $row['short_code_id'];
$short_code= $row['short_code'];
$company_id = $row['company_id'];
$keyword = $row['keyword'];

$data = '<soapenv:Envelope
	xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
	xmlns:v2="http://www.huawei.com.cn/schema/common/v2_1"
	xmlns:loc="http://www.csapi.org/schema/parlayx/sms/receive/v2_2/local">
	<soapenv:Header>
		<v2:RequestSOAPHeader>
			<v2:spId>'.$SPID.'</v2:spId>
			<v2:spPassword>'.$password.'</v2:spPassword>
			<v2:serviceId>'.$service_id.'</v2:serviceId>
			<v2:timeStamp>'.$timestamp.'</v2:timeStamp>
		</v2:RequestSOAPHeader>
	</soapenv:Header>
	<soapenv:Body>
		<loc:getReceivedSms>
			<loc:registrationIdentifier>'.$short_code.'</loc:registrationIdentifier>
		</loc:getReceivedSms>
	</soapenv:Body>
</soapenv:Envelope>';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://10.66.49.147:8310/ReceiveSmsService/services/ReceiveSms");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml; charset=utf-8", "Content-Length: " . strlen($data)));
$output = curl_exec($ch);
curl_close($ch);

// converting
$response = str_replace("<soap:Body>","",$output);
$response = str_replace("</soap:Body>","",$output);
//Save Log
//file_put_contents('log.txt',$correlator."--".$mobile_no."--".$status."--".date('Y-m-d h:i:sa'),FILE_APPEND | LOCK_EX);
file_put_contents('log.txt',$response,FILE_APPEND | LOCK_EX);
file_put_contents('log.txt', "\n", FILE_APPEND);
$doc = new DOMDocument();
$doc->loadXML($response);
$message = $doc->getElementsByTagName('message')->item(0)->nodeValue;
$date_time = $doc->getElementsByTagName('dateTime')->item(0)->nodeValue;
$short_code = substr($doc->getElementsByTagName('smsServiceActivationNumber')->item(0)->nodeValue,4);
$mobile_no="254".substr($doc->getElementsByTagName('senderAddress')->item(0)->nodeValue,4);//Remove "tel:" from the sender address
 
 if($message!=''){ 
// Take the response and generate some mark up
echo '<br/><div class="alert alert-success"><i class="fa fa-check-circle"></i>';
 //foreach($items as $item){
echo '
<response>
<h5>Message:-<font color="blue">'.$message.'</font></h5><br/>
<h5>Sender Address:- <font color="blue">'.$mobile_no.'</font></h5><br/>
<h5>Service Activation Number:- <font color="blue">'.$short_code.'</font></h5><br/>
<h5>Date/Time:-<font color="blue">'.$date_time.'</font></h5><br/>
</response>';

//Save these record into incoming messages 
$save = $conn->prepare("INSERT INTO  incoming_messages(service_id,short_code_id,message,mobile_no,keyword,company_id,msg_date,msg_read,msg_time)
VALUES('".$service_id."','".$short_code_id."', '".$message."','".$mobile_no."','".$company_id."',curdate(),'N',curtime())");
$save->execute();
 //Confirm contact
$confirm = $conn->prepare("SELECT id FROM contacts WHERE company_id='".$company_id."' AND mobile_no='".$mobile_no."' LIMIT 1");
$confirm->execute();
$confirm_count = $confirm->rowCount();
if($confirm_count==0){
 //Lets save this contact
 $save_contact = $conn->prepare("INSERT INTO contacts(mobile_no,contact_name,company_id,date_created,user_id,account_no)
VALUES('".$mobile_no."','Anonymous','".$company_id."','curdate()','2','N/A')");
$save_contact->execute();
}
// }
 echo '</div>';
 
 }
            
?>





