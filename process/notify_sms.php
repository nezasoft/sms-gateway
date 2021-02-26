<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- This application is used to notify the subscriber their message
 *or SMS request has been received
 * 23rd Nov 2018
 */
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 900);
ini_set('default_socket_timeout', 15);
ini_set('display_errors',true);

//Make a database connection
include("../../connection/connect.php");


class MySoapClient extends SoapClient {
    public function __doRequest($sRequest, $sLocation, $sAction, $iVersion, $iOneWay = 0) {
        $sRequest = str_replace('ns1', 'loc', $sRequest);
        $this->__last_request = $sRequest;

        return parent::__doRequest(ltrim($sRequest), $sLocation, $sAction, $iVersion, $iOneWay);
    }
}

//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
//User credentials
  /*$portaluser ='FON';
  $username='FON';
  $password ='e240b9820925d75275a506efc774effa';
  $partner_code ='1002';
  $cur_time = date('Ymd');
  $action ='activateSubscriber';
  $version ='1.0';
  $method ='act';
  $service ='activateSubscriber';
 	$subscriber_no = sanitize_string($_POST['SubscriberNo']);
	$serial_no = sanitize_string($_POST['serial_no']);
	$register_flag = 1;*/
  $SPID = 35000001 ;//Service provider ID
  $password = 'ZhlEXrrrAMfosSFpYPfdPCA';//Service provider password
  $serviceId = 3500000100001;
  $timestamp = date('Ymdhhmmss');
  $linkid = 07201312390000000006;
  $oa = 'tel:254722123456';
  $fa= 'tel:254722123456';
  $subscriber_no='254724802834';
  $sender_name='Frontier Opticals Network';
  $message='Welcome to Frontier Opticals Network';
  
function sendSMSRequest($SPID,$password,$serviceId,$timestamp,$linkid,$oa,$fa,$subscriber_no,$sender_name,$message){ 
  $xml = new XMLWriter();
  $xml->openMemory();
  $xml->startElementNS('soapenv','Header',null);//SOAP Header 
	$xml->startElementNS('v2','RequestSOAPHeader',null);//REQUEST SOAP Header
		$xml->startElementNS('v2','spId',null);//Service Provider ID
		$xml->Text($SPID);
		$xml->endElement();
		$xml->startElementNS('v2','spPassword',null);//Service Provider Password
		$xml->Text($password);
		$xml->endElement();
		$xml->startElementNS('v2','serviceId',null);//Service ID
		$xml->Text($serviceId);
		$xml->endElement();
		$xml->startElementNS('v2','timeStamp',null);//Timestamp
		$xml->Text($timestamp);
		$xml->endElement();
		$xml->startElementNS('v2','linkid',null);//Link ID
		$xml->Text($linkid); 
		$xml->endElement();
		$xml->startElementNS('v2','OA',null);//OA
		$xml->Text($oa); 
		$xml->endElement();
		$xml->startElementNS('v2','FA',null);//FA
		$xml->Text($fa); 
		$xml->endElement();
	$xml->endElement();//End of Header	
  $xml->endElement();//End of soap header
     //Start Body
	$xml->startElementNS('soapenv','Body',null);//Body
	     $xml->startElementNS('loc','sendSms',null):
			$xml->startElementNS('loc','addresses',null);//Addresses
			$xml->Text($subscriber_no);
			$xml->endElement();
			$xml->startElementNS('loc','senderName',null);//Sender Name
			$xml->Text($sender_name);
			$xml->endElement();
			$xml->startElementNS('loc','message',null);//Message
			$xml->Text($message);
			$xml->endElement();
				$xml->startElementNS('loc','receipRequest',null);//Receipt Request
					$xml->startElementNS('','endpoint',null);
					$xml->Text('http://10.138.30.123:9080/notify');
					$xml->endElement();
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
  $wsdl = 'http://196.201.214.115:5600/?wsdl';
  $client = new MySoapClient($wsdl, $options); 
  $client->__setLocation('http://196.201.214.115:5600/LTESharing');                                                                              
 $ns = 'http://www.csapi.org/schema/parlayx/sms/send/v2_2/local'; //Namespace of the web service.
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
                echo'
              		<response>
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

    
  }else{
	die("<font color='red'>You are not authorized to access this content.</font>");
	}

?>




