<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- This  program is used to synchronize subscription data to CSP applications.
 /*This application is used to update our local database that subscription status of mobile uses has been updated on the SDP
 * 12th Dec 2018
 */
date_default_timezone_set("Africa/Nairobi");
ini_set("display_errors",true);
  $SPID = '602078';//Service provider ID
  $real_pass = '#EDC4rfv';//Service provider password
  $serviceId = '0003062000001100';//Service ID
  $timestamp= date("YdmHis");
  $password = md5($SPID.$real_pass.$timestamp);
$data = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v2="http://www.huawei.com.cn/schema/common/v2_1"  xmlns:loc="http://www.csapi.org/schema/parlayx/data/sync/v1_0/local">
    <soapenv:Header/>
	<soapenv:Body>
		<loc:syncOrderRelation>
			<loc:userID>
				<ID>254721214848</ID>
				<type>0</type>
			</loc:userID>
			<loc:spID>'.$SPID.'</loc:spID>
			<loc:productID>MDSP2000052892</loc:productID>
			<loc:serviceID>'.$serviceId.'</loc:serviceID>
			<loc:serviceList>601399200000144</loc:serviceList>
			<loc:updateType>1</loc:updateType>
			<loc:updateTime>20181212113107</loc:updateTime>
			<loc:updateDesc>Addition</loc:updateDesc>
			<loc:effectiveTime>20181212113107</loc:effectiveTime>
			<loc:expiryTime>20181212113107</loc:expiryTime>
			<loc:extensionInfo>
				<namedParameters>
					<key>TransactionID</key>
					<value>1339730498361</value>
					<key>orderKey</key>
					<value>999000000009508556</value>
					<key>MDSPSUBEXPMODE</key>
					<value>1</value>
					<key>objectType</key>
					<value>1</value>
					<key>TraceUniqueID</key>
					<value>06212031580010010012</value>
					<key>rentSuccess</key>
					<value>false</value>
				</namedParameters>
			</loc:extensionInfo>
		</loc:syncOrderRelation>
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
// converting
            $response = curl_exec($ch); 
            curl_close($ch);

            // converting
            $response = str_replace("<soap:Body>","",$response);
            $response = str_replace("</soap:Body>","",$response);

            // convertingc to XML
              $xml = simplexml_load_string($response);   
              // Grabs the items in the response 
              $items = $xml->children('SOAP-ENV', true)->Body->children('ns1', true)->getReceivedSmsResponse; 
              // Take the response and generate some mark up
              echo '<br/><div class="alert alert-success">';
              foreach($items as $item){
                echo'
              		<response>
              			<h5>Message:- <font color="blue"> '.$item->result->message.'</font></h5><br/>
      						<h5>Sender Address:- <font color="blue"> '.$item->result->senderAddress.'</font></h5><br/>
      						<h5>Service Activation Number:- <font color="blue"> '.$item->result->smsServiceActivationNumber.'</font></h5><br/>
      						<h5>Date/Time:- <font color="blue"> '.$item->dateTime.'</font></h5><br/>
                	</response>';
              
              }
              echo '</div>'; $xml = simplexml_load_string($response);   
              // Grabs the items in the response 
              $items = $xml->children('SOAP-ENV', true)->Body->children('loc', true)->syncOrderRelationResponse; 
              // Take the response and generate some mark up
              echo '<br/><div class="alert alert-success">';
              foreach($items as $item){
                echo'<response>
              			<h5>Result:- <font color="blue"> '.$item->result.'</font></h5><br/>
						        <h5>Result Description:- <font color="blue"> '.$item->resultDescription.'</font></h5><br/>
						         <h5>Service Name:- <font color="blue"> '.$item->result->extensionInfo->namedParameters->key.'</font></h5><br/>
						        <h5>Service Description:- <font color="blue"> '.$item->result->extensionInfo->namedParameters->value.'</font></h5><br/>
                	</response>';            
              }
              echo '</div>';
?>
