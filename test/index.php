<?php
date_default_timezone_set("Africa/Nairobi");
ini_set("soap.wsdl_cache_enabled","0");
include("../connect/connect.php");
$SPID = '601957';//Service provider ID
$real_pass = '*789SFon';//Service provider password
$serviceId ='6019572000169778';//Service ID
$timestamp= date("YmdHis");
$password = md5($SPID.$real_pass.$timestamp);
//$criteria =$row['keyword'];
$endpoint = 'http://192.168.20.230/gateway/notify/sendsms/short_code/';
$sender_name ='40975';
$sender ='254724802834';
$linkid = '10180321075700630925';
$response_message ='Thank you for staying tuned to EBRU Tv.';

//echo phpinfo();

//echo 'Curl: ', function_exists('curl_version') ? 'Enabled' . "\xA" : 'Disabled' . "\xA";
//exit();
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
			//<v2:linkid>'.$linkid.'</v2:linkid>
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
//echo $data;

//sleep for 10 seconds before responding again to the SDP
//sleep(10);
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "http://10.66.49.147:8310/SendSmsService/services/SendSms");
//curl_setopt($ch, CURLOPT_URL,"http://www.nezasoft.net");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml; charset=utf-8", "Content-Length: " . strlen($data)));
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
//curl_setopt($ch, CURLOPT_PROXY, 'http://172.16.10.1:80');
$response = curl_exec($ch); 
curl_getinfo($ch);
curl_error($ch);
//curl_close($ch);


if(curl_errno($ch)){
    throw new Exception(curl_error($ch));
}

curl_close($ch);
//converting
$response = str_replace("<soap:Body>","",$response);
$response = str_replace("</soap:Body>","",$response);
print_r($response);
exit();

//Loads the XML
$xml = simplexml_load_string($response);   
// Grabs the items in the response 
$items = $xml->children('soapenv', true)->Body->children('ns1', true)->sendSmsResponse; 
// Take the response and generate some mark up              
foreach($items as $item){
$request_identifier = $item->result;
echo $request_identifier."</br>";
}

?>
