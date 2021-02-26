<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- This application will enable users query the delivery status of all the messages sent to subscribers
 * 12th Dec 2018
 */
 //Connect to DB
 include("../connect/connect.php"); 
date_default_timezone_set("Africa/Nairobi");
ini_set("display_errors",true);
//check if its an ajax request
 if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
 $short_code = sanitize_string($_POST['short_code']);
 $correlator = sanitize_string($_POST['correlator']);
 $message_id = sanitize_string($_POST['message_id']);
 try{
  $conn->beginTransaction(); 
  //Lets fetch the service id for this short code
  $get_service_id = $conn->prepare("SELECT service_id FROM short_codes WHERE id='".$short_code."' LIMIT 1");
  $get_service_id->execute();
  $get_service_row = $get_service_id->fetch(PDO::FETCH_ASSOC);
  $service_id = $get_service_row['service_id'];
 
  $spid = '601958';//Service provider ID
  $real_pass = '#Fon789S';//Service provider password
  $timestamp= date("YdmHis");
  $password = md5($SPID.$real_pass.$timestamp);

  $data = '<soapenv:Envelope
	xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
	xmlns:v2="http://www.huawei.com.cn/schema/common/v2_1"
	xmlns:loc="http://www.csapi.org/schema/parlayx/sms/send/v2_2/local">
	<soapenv:Header>
		<v2:RequestSOAPHeader>
			<v2:spId>'.$spid.'</v2:spId>
			<v2:spPassword>'.$password.'</v2:spPassword>
			<v2:serviceId>'.$service_id.'</v2:serviceId>
			<v2:timeStamp>'.$timestamp.'</v2:timeStamp>
		</v2:RequestSOAPHeader>
	</soapenv:Header>
	<soapenv:Body>
<loc:getSmsDeliveryStatus>
	<loc:requestIdentifier>'.$correlator.'</loc:requestIdentifier>
</loc:getSmsDeliveryStatus>
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
 
// converting
$response = str_replace("<soap:Body>","",$response);
$response = str_replace("</soap:Body>","",$response);
file_put_contents('log.txt',$response,FILE_APPEND | LOCK_EX);
file_put_contents('log.txt', "\n", FILE_APPEND);

$doc = new DOMDocument();
$doc->loadXML($response);
$mobile_no = substr($doc->getElementsByTagName('address')->item(0)->nodeValue,4);
$delivery_status = $doc->getElementsByTagName('deliveryStatus')->item(0)->nodeValue;

//confirm if record is already in db
$confirm=$conn->prepare("SELECT if FROM delivery_status WHERE message_id='".$message_id."' LIMIT 1");
$confirm->execute();
$confirm_count=$confirm->rowCount();
if($confirm_count==0){
//Save record
 $save = $con->prepare("INSERT INTO delivery_status(message_id,company_id,delivery_status,mobile_no)VALUES('".$message_id."','".$_SESSION['FON_G_COMPANY_ID']."','".$delivery_status."','".$mobile_no."')");
$save->execute();
 }
$conn->commit();
}catch(PDOException $e){
$conn->rollBack();
die("<div class='alert alert-danger'>Database Error:- ".$e->getMessage()."</div>");
}             
}else{
  die("<font color='red'>You are not authorized to access this content.</font>");
}	            
?>

