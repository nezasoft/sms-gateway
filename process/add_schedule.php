<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Add New Messaging Schedule
 * 6th Sept 2019
 */

include("../connect/connect.php");
ini_set("display_errors",true);
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
	$schedule_name = sanitize_string($_POST['schedule_name']);
	$frequency = sanitize_string($_POST['frequency']);
	$start_date = sanitize_string($_POST['start_date']);
	$end_date = sanitize_string($_POST['end_date']);
	$schedule_time = sanitize_string($_POST['schedule_time']);
	$status = sanitize_string($_POST['status']);
try{
$conn->beginTransaction();
//Check if record exist
$confirm = $conn->prepare("SELECT id AS schedule_id FROM schedules WHERE schedule_name='".$schedule_name."' AND company_id='".$_SESSION['FON_G_COMPANY_ID']."' LIMIT 1 ");
$confirm->execute();
$count_confirm = $confirm->rowCount();

if($count_confirm>=1){
die("<div class='alert alert-danger'>* This schedule  already exists in system.</div>");
}

//Lets save this record
$save = $conn->prepare("INSERT INTO schedules(schedule_name,start_date,end_date,schedule_time,create_date,active,user_id,company_id,frequency_id)
						VALUES('".$schedule_name."','".$start_date."','".$end_date."','".$schedule_time."',curdate(),'".$status."','".$_SESSION['FON_G_USER_ID']."','".$_SESSION['FON_G_COMPANY_ID']."','".$frequency."')");
$save->execute();
$row_count = $save->rowCount();
$conn->commit();
if($row_count>=1){
echo '<script>$("#form_content").empty();</script>';
echo "<div class='alert alert-success'><i class='fa fa-check-circle' ></i> New schedule type  added successfully!</div>";
echo '<script>
		 window.setTimeout(function(){
		// Move to a new location or you can do something else
		window.location.href = "?settings&action=new_level";
		}, 3000);
	  </script>';
}else{
die("<div class='alert alert-danger'>Error saving record!</div>");
}	  

}catch(PDOException $e){
$conn->rollBack();
 die("<div class='alert alert-danger'>Database Error:- ".$e->getMessage()."</div>");
}

}else{
die("<font color='red'>You are not authorized to access this content.</font>");
}
?>
