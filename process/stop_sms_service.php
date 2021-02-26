<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- This  program ends a user's subscription to stop SMS service.
 * 26th Nov 2018
 */
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 900);
ini_set('default_socket_timeout', 15);
ini_set('display_errors',true);

//Make a database connection
include("../connect/connect.php");

class MySoapClient extends SoapClient {
    public function __doRequest($sRequest, $sLocation, $sAction, $iVersion, $iOneWay = 0) {
        $sRequest = str_replace('ns1', 'loc', $sRequest);
        $this->__last_request = $sRequest;
        return parent::__doRequest(ltrim($sRequest), $sLocation, $sAction, $iVersion, $iOneWay);
    }
}

//check if its an ajax request
//if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
  $SPID = '601957';//Service provider ID
  
  $real_pass = '#Fon789S';//Service provider password
  $timestamp= date("YdmHis");
  $password = md5($SPID.$real_pass.$timestamp);
  $service_id = '60195720001697781';
  $correlator = 'FON_95A5TGGkRR';
function sendSMSRequest($SPID,$password,$serviceId,$timestamp){ 
  $xml = new XMLWriter();
  $xml->openMemory();
  $xml->startElementNS('soapenv','Header',null);//SOAP Header 
	$xml->startElementNS('','RequestSOAPHeader','xlmns="http://www.huwei.com.cn/schema/common/v2_1"');//Request SOAP Header
	    $xml->startElementNS('','spId',null);//Service Prodiver ID
		$xml->Text($SPID);
		$xml->endElement();
		$xml->startElementNS('','spPassword',null);//Service Provider Password
		$xml->Text($password);
		$xml->endElement();
		$xml->startElementNS('','serviceId',null);//Service ID
		$xml->Text($service_id);
		$xml->endElement();
		$xml->startElementNS('','timeStamp',null);//TimeStamp
		$xml->Text($timestamp);
		$xml->endElement();		
	$xml->endElement();//End of RequestSOAPHeader	
  $xml->endElement();//End of soap header
     //Start Body
	$xml->startElementNS('soapenv','Body',null);//Body
	     $xml->startElementNS('loc','stopSmsNotification',null);           			
			$xml->startElementNS('loc','correlator',null);//Correlator
			$xml->Text($correlator);
			$xml->endElement();
		$xml->endElement();	
	$xml->endElement(); 
  //Convert it to a valid SoapVar
  $args = new SoapVar($xml->outputMemory(), XSD_ANYXML);
  $options = array(
		'uri'=>'http://schemas.xmlsoap.org/soap/envelope/',
		'style'=>SOAP_RPC,
		'soap_version'=>SOAP_1_1,
		'cache_wsdl'=>WSDL_CACHE_NONE,
		'connection_timeout'=>30,
		'trace'=>true,
		'encoding'=>'UTF-8',
		'exceptions'=>true		
	);
  $wsdl = 'http://10.66.49.147:8310/SmsNotificationManagerService/services/SmsNotificationManager';
  $client = new MySoapClient($wsdl, $options); 
  $client->__setLocation('http://10.66.49.147:8310/SmsNotificationManagerService/services/SmsNotificationManager');                                                                              
 $ns = 'http://www.csapi.org/schema/parlayx/sms/notification_manager/v2_3/local'; //Namespace of the web service.
 $header = new SOAPHeader($ns,'Body','',false);
//set the Headers of Soap Client. 
 $client->__setSoapHeaders($header);
 
  try{
               $result = $client->__SoapCall($action, array($args));	
              //Parse the SOAP response to Human Readable format
              $response = $client->__getLastResponse();
              // Loads the XML
              $xml = simplexml_load_string($response);   
              // Grabs the items in the response 
              $items = $xml->children('SOAP-ENV', true)->Body->children('ns1', true)->ServiceReply; 
              // Take the response and generate some mark up
              echo '<br/><div class="alert alert-success">';
              foreach($items as $item){
                echo'<response>
              			<h5>Response Message:- <font color="blue"> '.$item->ResponseMsg.'</font></h5><br/>
                	</response>';            
              }
              echo '</div>';
  }catch(Exception $e){
    $result = FALSE;
	echo $e->getMessage();
  } 
  return $result; 
 }  
 /* }else{
	die("<font color='red'>You are not authorized to access this content.</font>");
	}*/
?>
