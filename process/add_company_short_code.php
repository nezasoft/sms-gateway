<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Add New Company Short Code
 * 26th Dec 2018
 */
date_default_timezone_set("Africa/Nairobi");

include("../connect/connect.php");
ini_set("display_errors",true);
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
	$company = sanitize_string($_POST['company']);
	$short_code = sanitize_string($_POST['short_code']);
	$endpoint = sanitize_string($_POST['endpoint']);
	$message = sanitize_string($_POST['message']);
	$keyword = sanitize_string($_POST['keyword']);
        $sender_name = sanitize_string($_POST['sender_name']);
	$short_code_id=$short_code;
	
	//Lets Generate the Correlator for this request
   function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
   }
   $correlator = 'FON'.generateRandomString(); 
   //Save the correlator on session
   $_SESSION['FON_CORRELATOR']=$correlator;

try{
$conn->beginTransaction();
//Check if record exist
$confirm = $conn->prepare("SELECT id AS company_short_code_id FROM company_short_codes WHERE short_code_id='".$short_code."' AND company_id='".$company."' AND keyword='".$keyword."' ");
$confirm->execute();
$count_confirm = $confirm->rowCount();

if($count_confirm>=1){
die("<div class='alert alert-danger'>* This short code is already registered for this company</div>");
}

//Lets get the service id for the short code
$query = $conn->prepare("SELECT service_id,short_code,product_id FROM short_codes WHERE id='".$short_code."' LIMIT 1");
$query->execute();
$query_row = $query->fetch(PDO::FETCH_ASSOC);
$product_id = $query_row['product_id'];
$short_code = $query_row['short_code'];//Short Code
$serviceId = $query_row['service_id'];//Service ID
if($product_id==1){//Short Codes
$SPID = '601957';//Service provider ID for Short Codes
//Before we do anything lets get current password for the short code service to the SDP
$get_sdp_pass = $conn->prepare("SELECT sys_password FROM sys_settings WHERE section_type='SC' LIMIT 1");
$get_sdp_pass->execute();
$get_sdp_pass_row = $get_sdp_pass->fetch(PDO::FETCH_ASSOC);
$sdp_password = $get_sdp_pass_row['sys_password'];
}elseif($product_id==2){//Bulk SMS
$SPID = '601958';//Service provider ID for Bulk SMS
//Before we do anything lets get current password for the short code service to the SDP
$get_sdp_pass = $conn->prepare("SELECT sys_password FROM sys_settings WHERE section_type='BK' LIMIT 1");
$get_sdp_pass->execute();
$get_sdp_pass_row = $get_sdp_pass->fetch(PDO::FETCH_ASSOC);
$sdp_password = $get_sdp_pass_row['sys_password'];
}

$real_pass = $sdp_password;//Service provider password
$timestamp= date("YmdHis");//Current Date
$password = md5($SPID.$real_pass.$timestamp);//MD5 Hashed Password
$criteria = $keyword;

//Lets Invoke the startSMSNotification Request
 $data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v2="http://www.huawei.com.cn/schema/common/v2_1"  xmlns:loc="http://www.csapi.org/schema/parlayx/sms/notification_manager/v2_3/local">
    <soapenv:Header>
        <RequestSOAPHeader xmlns="http://www.huawei.com.cn/schema/common/v2_1">
            <spId>'.$SPID.'</spId>
            <spPassword>'.$password.'</spPassword>
            <serviceId>'.$serviceId.'</serviceId>
            <timeStamp>'.$timestamp.'</timeStamp>
        </RequestSOAPHeader>
    </soapenv:Header>
    <soapenv:Body>
        <loc:startSmsNotification>
            <loc:reference>
                <endpoint>'.$endpoint.'</endpoint>
                <interfaceName>notifySmsReception</interfaceName>
                <correlator>'.$correlator.'</correlator>
            </loc:reference>
            <loc:smsServiceActivationNumber>'.$short_code.'</loc:smsServiceActivationNumber>
            <!--Optional:-->
            <loc:criteria>'.$criteria.'</loc:criteria>
        </loc:startSmsNotification>
    </soapenv:Body>
</soapenv:Envelope>';
 
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://10.66.49.147:8310/SmsNotificationManagerService/services/SmsNotificationManager");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml; charset=utf-8", "Content-Length: " . strlen($data)));

$response = curl_exec($ch);
curl_close($ch);

echo "<div class='alert alert-success'><i class='fa fa-check-circle' ></i> Client short code activated <br/> ".$response."</div>";

//Lets save this record
$save = $conn->prepare("INSERT INTO company_short_codes(short_code_id,sender_name,company_id,response_message,keyword,endpoint,correlator,date_created,user_id)
VALUES('".$short_code_id."','".$sender_name."','".$company."','".$message."','".$keyword."','".$endpoint."','".$_SESSION['FON_CORRELATOR']."',now(),'".$_SESSION['FON_G_USER_ID']."')");
$save->execute();
$row_count = $save->rowCount();
$conn->commit();
if($row_count>=1){
//Lets Invoke the startSMSNotification Request
 $data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v2="http://www.huawei.com.cn/schema/common/v2_1"  xmlns:loc="http://www.csapi.org/schema/parlayx/sms/notification_manager/v2_3/local">
    <soapenv:Header>
        <RequestSOAPHeader xmlns="http://www.huawei.com.cn/schema/common/v2_1">
            <spId>'.$SPID.'</spId>
            <spPassword>'.$password.'</spPassword>
            <serviceId>'.$serviceId.'</serviceId>
            <timeStamp>'.$timestamp.'</timeStamp>
        </RequestSOAPHeader>
    </soapenv:Header>
    <soapenv:Body>
        <loc:startSmsNotification>
            <loc:reference>
                <endpoint>'.$endpoint.'</endpoint>
                <interfaceName>notifySmsReception</interfaceName>
                <correlator>'.$correlator.'</correlator>
            </loc:reference>
            <loc:smsServiceActivationNumber>'.$short_code.'</loc:smsServiceActivationNumber>
            <!--Optional:-->
            <loc:criteria>'.$criteria.'</loc:criteria>
        </loc:startSmsNotification>
    </soapenv:Body>
</soapenv:Envelope>';
 
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://10.66.49.147:8310/SmsNotificationManagerService/services/SmsNotificationManager");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml; charset=utf-8", "Content-Length: " . strlen($data)));

$response = curl_exec($ch);
curl_close($ch);

//Update Statuses
$update_s1 = $conn->prepare("UPDATE short_codes SET status='1' WHERE id='".$short_code_id."'  LIMIT 1");
$update_s1->execute();

$update_s2 = $conn->prepare("UPDATE company_short_codes SET status='1' WHERE short_code_id='".$short_code_id."'  LIMIT 1");
$update_s2->execute();
	
echo '<script>$("#form_content").empty();</script>';
echo "<div class='alert alert-success'><i class='fa fa-check-circle' ></i> Client short code activated <br/> ".$response."</div>";
/*echo '<script>
		 window.setTimeout(function(){
		// Move to a new location or you can do something else
		window.location.href = "?short_codes&action=view_short_codes&client_id='.$company.'";
		}, 3000);
	  </script>';*/
}else{
die("<div class='alert alert-danger'>Error saving record!</div>");
}	  

}catch(PDOException $e){
$conn->rollBack();
 die("<div class='alert alert-danger'>Database Error:- ".$e->getMessage()."</div>");
}

}else{
die("<font color='red'>You are not authorized to access this content.</font>");
}
?>
