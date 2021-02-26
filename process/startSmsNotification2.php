<?php 
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- This programs enables users to register for SMS subscriptions
 * 26th Nov 2018
 */
date_default_timezone_set("Africa/Nairobi");
ini_set("display_errors",true);
  $SPID = '602078';//Service provider ID
  $real_pass = '#EDC4rfv';//Service provider password
  $serviceId = '0003062000001100';//Service ID
  $timestamp= date("YmdHis");
  $password = md5($SPID.$real_pass.$timestamp);
  $short_code = 6027;
  $criteria ="INTERNET";
echo $password;
  $data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" 
  xmlns:v2="http://www.huawei.com.cn/schema/common/v2_1" 
   xmlns:loc="http://www.csapi.org/schema/parlayx/sms/notification_manager/v2_3/local">
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
                <endpoint>>https://192.168.20.230/fonsms/notify</endpoint>
                <interfaceName>notifySmsReception</interfaceName>
                <correlator>12345678</correlator>
            </loc:reference>
            <loc:smsServiceActivationNumber>6027</loc:smsServiceActivationNumber>
            <!--Optional:-->
            <loc:criteria>Internet</loc:criteria>
        </loc:startSmsNotification>
    </soapenv:Body>
</soapenv:Envelope>';
 
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "http://10.66.49.198:8310/SmsNotificationManagerService/services/SmsNotificationManager");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERPWD, "");
curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml; charset=utf-8", "Content-Length: " . strlen($data)));
$output = curl_exec($ch);
curl_close($ch);
 
echo $output."\n";
/*
            // converting
            $response = curl_exec($ch); 
            curl_close($ch);

            // converting
            $response1 = str_replace("<soap:Body>","",$response);
            $response2 = str_replace("</soap:Body>","",$response1);

            // convertingc to XML
            $parser = simplexml_load_string($response2);
            // user $parser to get your data out of XML response and to display it.
            */
    ?>
