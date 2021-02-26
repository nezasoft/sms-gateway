<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Once an SMS has been sent to the SDP an asynchronous request with the delivery status is sent back to Frontier's
  gateway for processing. This application captures the requesting from the SDP containg the status of the message sent.
  The sample soap response from the SDP is as follows:-

<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
   <soapenv:Header>
      <ns1:NotifySOAPHeader xmlns:ns1="http://www.huawei.com.cn/schema/common/v2_1">
         <ns1:spId>602078</ns1:spId>
         <ns1:serviceId>6020782000004251</ns1:serviceId>
         <ns1:traceUniqueID>504021503231901221403144441003</ns1:traceUniqueID>
      </ns1:NotifySOAPHeader>
   </soapenv:Header>
   <soapenv:Body>
      <ns2:notifySmsDeliveryReceipt xmlns:ns2="http://www.csapi.org/schema/parlayx/sms/notification/v2_2/local">
         <ns2:correlator>FONPDVZg0jXbyvGh44</ns2:correlator>
         <ns2:deliveryStatus>
            <address>tel:254724802834</address>
            <deliveryStatus>DeliveryImpossible</deliveryStatus>
         </ns2:deliveryStatus>
      </ns2:notifySmsDeliveryReceipt>
   </soapenv:Body>
</soapenv:Envelope>

 *23rd Jan 2019
 */
//disable cache
ini_set("soap.wsdl_cache_enabled","0");
//Capture the post request from the SDP
$response = trim(file_get_contents("php://input"));
 //converting
$response = str_replace("<soap:Body>","",$response);
$response = str_replace("</soap:Body>","",$response);
$xml = simplexml_load_string($response); 
$doc = new DOMDocument();
$doc->loadXML($response);
$correlator = $doc->getElementsByTagName('correlator')->item(0)->nodeValue;
$status = $doc->getElementsByTagName('deliveryStatus')->item(1)->nodeValue;
$mobile_no=substr($doc->getElementsByTagName('address')->item(0)->nodeValue,4);//Remove "tel:" from the sender address

echo '<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:loc="http://www.csapi.org/schema/parlayx/sms/notification/v2_2/local">
   <soapenv:Header />
   <soapenv:Body>
      <loc:notifySmsDeliveryReceiptResponse />
   </soapenv:Body>
</soapenv:Envelope>';

//sleep for 5 seconds
sleep(5);

//Save Log
file_put_contents('log.txt',$correlator."--".$mobile_no."--".$status."--".date('Y-m-d h:i:sa'),FILE_APPEND | LOCK_EX);
file_put_contents('log.txt', "\n", FILE_APPEND);

include("../../connect/connect.php");
//Lets use the correlator to get the message id and company_id
$get_data= $conn->prepare("SELECT id AS message_id,company_id FROM outgoing_messages WHERE correlator='".$correlator."' LIMIT 1");
$get_data->execute();
$get_data_row = $get_data->fetch(PDO::FETCH_ASSOC);
$company_id = $get_data_row['company_id'];
$message_id = $get_data_row['message_id'];

//Update outgoing message status
$update_outgoing_status = $conn->prepare("INSERT INTO delivery_status(message_id,company_id,mobile_no,delivery_status)VALUES('".$message_id."','".$company_id."','".$mobile_no."','".$status."')");
$update_outgoing_status->execute();



?>