<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Update incoming message read status
 * 30th Dec 2018
 */

include("../connect/connect.php");
ini_set("display_errors",true);
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
	$message_id = sanitize_string($_POST['message_id']);

try{
$conn->beginTransaction();
//Check if record exist
$confirm = $conn->prepare("SELECT id AS read_id FROM message_reads WHERE message_id='".$message_id."'  AND user_id='".$_SESSION['FON_G_USER_ID']."' LIMIT 1");
$confirm->execute();
$count_confirm = $confirm->rowCount();

if($count_confirm>=1){
exit();//Dont do anything
}

//Update message read status
$update_message_status = $conn->prepare("UPDATE incoming_messages SET msg_read='Y' WHERE id='".$message_id."' LIMIT 1");
$update_message_status->execute();

//Insert into message reads
$save_reads = $conn->prepare("INSERT INTO message_reads(message_id,user_id,read_date,read_time)
VALUES('".$message_id."','".$_SESSION['FON_G_USER_ID']."',curdate(),curtime())");
$save_reads->execute();

$conn->commit();	  
}catch(PDOException $e){
$conn->rollBack();
 die("<div class='alert alert-danger'>Database Error:- ".$e->getMessage()."</div>");
}

}else{
die("<font color='red'>You are not authorized to access this content.</font>");
}
?>