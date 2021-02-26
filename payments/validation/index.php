<?php 
header("Content-Type:application/json"); 
/*if (!isset($_GET["token"])){
echo "Technical error";
exit();
}
 file_put_contents('log.txt',$_GET['token'],FILE_APPEND | LOCK_EX);
file_put_contents('log.txt', "\n", FILE_APPEND);

if($_GET["token"]!='KMp8TvJUnugjVT9h'){
echo "Invalid authorization";
exit();
}*/
/* 
here you need to parse the json format 
and do your business logic e.g. 
you can use the Bill Reference number 
or mobile phone of a customer 
to search for a matching record on your database. 
*/ 
/* 
Reject an Mpesa transaction 
by replying with the below code 
*/ 
//echo '{"ResultCode":1, "ResultDesc":"Failed", "ThirdPartyTransID": 0}'; 
/* 
Accept an Mpesa transaction 
by replying with the below code 
*/ 
echo '{"ResultCode":0, "ResultDesc":"Success", "ThirdPartyTransID": 0}';
 
?>
