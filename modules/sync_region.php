<?php
/* @author Walter Omedo Sync Messages with wards which they are originating from */



$conn = new PDO('mysql:host=localhost;dbname=fonsms;charset=utf8', 'root', 'fr0nt!er');
$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);


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
	//Get Wards
	$wards = $conn->prepare("SELECT id AS ward_id, ward_name FROM wards ORDER BY id ASC");
	$wards->execute();
	$ward_rows=$wards->fetchAll(PDO::FETCH_ASSOC);

		foreach($ward_rows as $ward_row){
		$ward_name = $ward_row['ward_name'];		
		//Get message
		$get_message = $conn->prepare("SELECT id AS message_id FROM incoming_messages WHERE message REGEXP '[[:<:]]".$ward_name."[[:>:]]' AND id='".$message_id."' LIMIT 1 ");
		$get_message->execute();
		$get_msg_count = $get_message->rowCount();		
		if($get_msg_count==1){
		//Save message county
		$insert = $conn->prepare("INSERT INTO message_regions(message_id,region_name)VALUES('".$message_id."','".$ward_name."')");
		$insert->execute();
		}
		

           }

	}
//Check sync again
/*$check_sync = $conn->prepare("SELECT id AS msg_id FROM message_regions WHERE message_id='".$message_id."' LIMIT 1 ");
$check_sync->execute();
$sync_count = $check_sync->rowCount();
if($sync_count==0){
//Lets update the other messages as Unknown Location
	$insert = $conn->prepare("INSERT INTO message_regions(message_id,region_name)VALUES('".$message_id."','Unspecified')");
	$insert->execute();	
	
}*/

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

