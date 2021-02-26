<?php
/* @author Walter Omedo Sync Messages with counties which they are originating from */

include("../connect/connect.php");
ini_set("display_errors",true);

try{
$conn->beginTransaction();
//Get all incoming messages
$im = $conn->prepare("SELECT id AS message_id, message FROM incoming_messages ORDER BY id ASC");
$im->execute();

$im_rows = $im->fetchAll(PDO::FETCH_ASSOC);

foreach($im_rows as $im_row){
//Lets check if this record has been synced
$message_id = $im_row['message_id'];

$message = $im_row['message'];
$check_sync = $conn->prepare("SELECT id AS msg_id FROM message_regions WHERE message_id='".$message_id."' LIMIT 1 ");
$check_sync->execute();

$sync_count = $check_sync->rowCount();

	if($sync_count==0){

	//Get counties
	$counties = $conn->prepare("SELECT id AS county_id, county_name FROM county ORDER BY id ASC");
	$counties->execute();
	$county_rows=$counties->fetchAll(PDO::FETCH_ASSOC);

		foreach($county_rows as $county_row){
		$county_name = $county_row['county_name'];
       
		//Get message
		$get_message = $conn->prepare("SELECT id AS message_id FROM incoming_messages WHERE message REGEXP '[[:<:]]".$county_name."[[:>:]]' AND id='".$message_id."' LIMIT 1 ");
		$get_message->execute();
		$get_msg_count = $get_message->rowCount();
		
		if($get_msg_count==1){
		//Save message county
		$insert = $conn->prepare("INSERT INTO message_regions(message_id,region_name)VALUES('".$message_id."','".$county_name."')");
		$insert->execute();
		}
		

           }
	}
}
$conn->commit();
echo "All data synced successfully";
}catch(PDOException $e){
$conn->rollBack();
//Log Error
//Save Log
file_put_contents('log.txt',$e->getMessage()."--".date('Y-m-d h:i:sa'),FILE_APPEND | LOCK_EX);
file_put_contents('log.txt', "\n", FILE_APPEND);
}

?>

