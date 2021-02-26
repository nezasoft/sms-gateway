<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Assign SMS Package
 * 18th July 2019
 */

include("../connect/connect.php");
ini_set("display_errors",true);
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
	$company= sanitize_string($_POST['company']);
	$pkg= sanitize_string($_POST['pkg']);
	$no_sms= sanitize_string($_POST['no_sms']);

try{
$conn->beginTransaction();
//Check if record exist
$confirm = $conn->prepare("SELECT id AS request_id FROM client_bulk_sms WHERE client_id='".$company."'  LIMIT 1");
$confirm->execute();
$count_confirm = $confirm->rowCount();

if($count_confirm>=1){
die("<div class='alert alert-danger'>* This client has already been assigned an SMS package.</div>");
}
$active_date = date('Y-m-d');
$expire_date = date('Y-m-d', strtotime("+60 days"));
//Lets save this record
$save = $conn->prepare("INSERT INTO client_bulk_sms(sms_package_id,client_id,amount,balance,activation_date,expiry_date)VALUES('".$pkg."','".$company."','".$no_sms."','".$no_sms."','".$active_date."','".$expire_date."')");
$save->execute();
$row_count = $save->rowCount();
$conn->commit();
if($row_count>=1){
echo '<script>$("#form_section").empty();</script>';
echo "<div class='alert alert-success'><i class='fa fa-check-circle' ></i> SMS Package assigned!</div>";
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
