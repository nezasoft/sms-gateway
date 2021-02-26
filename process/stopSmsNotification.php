<?php


/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- This programs disables use of short for sms notifications
 * 12th Dec 2018
 */
 ///Code will invoke the  "stopSMSNotification" on the Safaricom's SDP(Service Delivery Platform)
date_default_timezone_set("Africa/Nairobi");
ini_set("display_errors",true);
include('../connect/connect.php');
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
  $product_id = $_POST['product_id'];
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
  $service_id = $_POST['service_id'];
  $correlator = $_POST['correlator'];
  $data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v2="http://www.huawei.com.cn/schema/common/v2_1"  xmlns:loc="http://www.csapi.org/schema/parlayx/sms/notification_manager/v2_3/local">
    <soapenv:Header>
        <RequestSOAPHeader xmlns="http://www.huawei.com.cn/schema/common/v2_1">
            <spId>'.$SPID.'</spId>
            <spPassword>'.$password.'</spPassword>
            <serviceId>'.$service_id.'</serviceId>
            <timeStamp>'.$timestamp.'</timeStamp>
        </RequestSOAPHeader>
    </soapenv:Header>
    <soapenv:Body>
        <loc:stopSmsNotification>
            <loc:correlator>'.$correlator.'</loc:correlator>
        </loc:stopSmsNotification>
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
$output = curl_exec($ch);
curl_close($ch);
$response = str_replace("<soap:Body>","",$output);
$response = str_replace("</soap:Body>","",$output);

echo "<div class='alert alert-success'><i class='fa fa-check-circle' ></i> Success ".$response."</div>";

}else{
die("<font color='red'>You are not authorized to access this content.</font>");
}
?>

