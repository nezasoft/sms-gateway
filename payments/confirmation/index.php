

<?php
include('../connect/index.php');
header("Content-Type:application/json");
/*if (!isset($_GET["token"])){
echo "Technical error";
exit();
}
file_put_contents('log.txt',$_GET['token'],FILE_APPEND | LOCK_EX);
file_put_contents('log.txt', "\n", FILE_APPEND);

$password="KMp8TvJUnugjVT9h";
if($_GET["token"]!=$password){
echo "Invalid authorization";
exit();
}
*/
if (!$request=file_get_contents('php://input')){
echo "Invalid input";
exit();
}

//Put the json string that we received from Safaricom to an array
$array = json_decode($request, true);
$transactiontype= $array['TransactionType']; 
$transid=$array['TransID']; 
$transtime= $array['TransTime']; 
$transamount= $array['TransAmount']; 
$businessshortcode= $array['BusinessShortCode']; 
$billrefno= $array['BillRefNumber']; 
$invoiceno= $array['InvoiceNumber']; 
$msisdn= $array['MSISDN']; 
$orgaccountbalance= $array['OrgAccountBalance']; 
$firstname=str_replace("'",'-',$array['FirstName']); 
$middlename=str_replace("'",'-',$array['MiddleName']); 
$lastname=str_replace("'",'-',$array['LastName']); 
 
$sql=$conn->prepare("INSERT INTO transactions(TransactionType,TransID,TransTime,TransAmount,BusinessShortCode,BillRefNumber,InvoiceNumber,MSISDN,FirstName,MiddleName,LastName,OrgAccountBalance) VALUES( 
'".$transactiontype."', '".$transid."', '".$transtime."','".$transamount."','".$businessshortcode."','".$billrefno."','".$invoiceno."','".$msisdn."','".$firstname."','".$middlename."','".$lastname."','".$orgaccountbalance."')");
$sql->execute();
$row_count = $sql->rowCount();

if($row_count>=1){
echo '{"ResultCode":0,"ResultDesc":"Confirmation received successfully"}';
}

//Lets send this curl request to .240 server for processing
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"http://192.168.20.240/mpesa_services/");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,"account_no=$billrefno&amount=$transamount&trans_id=$transid");
// Receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
curl_close ($ch);

//Lets send sames  request to .230 server for processing
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL,"http://192.168.20.230/nuerp/accounting/process/save_mpesa_receipt.php");
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS,"account_no=$billrefno&amount=$transamount&trans_id=$transid");
// Receive server response ...
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_exec($ch);
curl_close ($ch);

//Lets update the client listing
$account_no = $billrefno;

$client_no = substr($account_no,2,-3);//Removes the first 2 and last 3 characters from the string

try{
$conn->BeginTransaction();
//Lets check if this client is in our DB
$check = $conn->prepare("SELECT id AS client_id FROM nyayo_billing.clients WHERE client_no='".$client_no."' LIMIT 1");
$check->execute();
$check_count_rows = $check->rowCount();

if($check_count_rows==1){//found
//perform update
$update = $conn->prepare("UPDATE nyayo_billing.clients SET last_payment_amount='".$transamount."',last_payment_date='".$transtime."' WHERE client_no='".$client_no."' LIMIT 1");
$update->execute();
}
$conn->commit();

}catch(PDOException $e){

$conn->rollBack();


}

?>




