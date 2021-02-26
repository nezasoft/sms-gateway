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

echo '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:loc="http://www.csapi.org/schema/parlayx/data/sync/v1_0/local">
   <soapenv:Header />
   <soapenv:Body>
      <loc:syncOrderRelationResponse>
         <loc:result>0</loc:result>
         <loc:resultDescription>OK</loc:resultDescription>
         <!--Optional:-->
         <loc:extensionInfo>
            <!--Zero or more repetitions:-->
            <namedParameters>
               <key>6337_SPORTS</key>
               <value>6337</value>
            </namedParameters>
         </loc:extensionInfo>
      </loc:syncOrderRelationResponse>
   </soapenv:Body>
</soapenv:Envelope>';


// sleep for 10 seconds
sleep(10);
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


//Save details in log file
file_put_contents('log.txt',$service_id.'--'.$mobile_no.'--'.$product_id.'--'.$update_time.'--'.$update_desc.'--'.date('Y-m-d h:i:sa'),FILE_APPEND | LOCK_EX);
file_put_contents('log.txt', "\n", FILE_APPEND);

//Connect to Database
include("../../../../../connect/connect.php");

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


//Get short code id
$get_code = $conn->prepare("SELECT csc.id AS company_short_code_id, sc.short_code, sc.id AS short_code_id, csc.company_id FROM company_short_codes AS csc LEFT JOIN short_codes AS sc ON sc.id = csc.short_code_id WHERE sc.service_id='".$service_id."' ");
$get_code->execute();
$get_code_row = $get_code->fetch(PDO::FETCH_ASSOC);
$short_code = $get_code_row['short_code'];
$sender_name = $get_code_row['short_code'];
$company_id = $get_code_row['company_id'];
$short_code_id = $get_code_row['short_code_id'];
$company_short_code_id = $get_code_row['company_short_code_id'];
try{
$conn->beginTransaction();
//Confirm if subscriber already exists
$confirm = $conn->prepare("SELECT id FROM  subscribers WHERE company_id='".$company_id."' AND company_short_code_id='".$short_code_id."' AND mobile_no='".$mobile_no."' LIMIT 1");
$confirm->execute();
$confirm_count = $confirm->rowCount();

if($confirm_count==0){//Insert new record
$save = $conn->prepare("INSERT INTO subscribers(company_short_code_id,mobile_no,company_id,date_created,time_created,active)
VALUES('".$company_short_code_id."','".$mobile_no."','".$company_id."',curdate(),curtime(),'1')");
$save->execute();

if($update_desc=='Addition'){
  $save = $conn->prepare("INSERT INTO subscribers(company_short_code_id,mobile_no,company_id,date_created,time_created,active)
                        VALUES('".$company_short_code_id."','".$mobile_no."','".$company_id."',curdate(),curtime(),'1')");
  $save->execute();                      
  $message = "You have been subscribed to FON Sports. You will receive daily sports update at a cost of KES 5.00 per day";
  }

}else{//Update existing record
  if($update_desc=='Deletion'){
    if($update_type=='1'){
    $update = $conn->prepare("UPDATE subscribers SET  active='1' WHERE mobile_no='".$mobile_no."' AND company_id='".$company_id."' LIMIT 1");
    $update->execute();
    //Send Customers message activation message
    $message = "You have been subscribed to FON Sports. You will continue to receive daily sports updates at a cost of KES 5.00 per day";
    }elseif($update_type='0'){
    $update = $conn->prepare("UPDATE subscribers SET  active='0' WHERE mobile_no='".$mobile_no."' AND company_id='".$company_id."' LIMIT 1");
    $update->execute();
    //Send customers message deactivation message
    $message = "You have been unsubscribed from FON Sports. You will no longer receive daily sports updates. Thank you for being part of us.";
    
    }
  }

}
/*
//Send Request to the SDP to relay the message to the mobile subscriber
$SPID = '602078';//Service provider ID
$real_pass = '#Fon789S';//Service provider password
$timestamp= date("YmdHis");
$password = md5($SPID.$real_pass.$timestamp);
$endpoint = 'http://192.168.20.230/gateway/notify/sendsms/';//Callback URL 

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
			$data .='<loc:addresses>tel:'.$mobile_no.'</loc:addresses>';
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

//Lets save this message to our DB
   $query = $conn->prepare("INSERT INTO outgoing_messages(sms_count,message,short_code_id,mobile_no,company_id,date_sent,time_sent,user_id,delivery_status,correlator)
   VALUES('1','".$message."','".$short_code_id."','".$short_code."','".$company_id."',curdate(),curtime(),'2','Pending','".$correlator."')");
    $query->execute();
   //Lets use mysql last_insert_id() to get the message id
   $query_last_row = $conn->prepare("SELECT MAX(id) as last_id FROM outgoing_messages WHERE company_id='".$company_id."' AND user_id='2'");
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

}*/
$conn->commit();
}catch(PDOException $e){
//Perform rollback
$conn->rollBack();
//Save details in log file
file_put_contents('log.txt',$e->getMessage(),FILE_APPEND | LOCK_EX);
file_put_contents('log.txt', "\n", FILE_APPEND);



}



?>