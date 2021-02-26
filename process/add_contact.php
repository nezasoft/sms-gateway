<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Add New Contact
 * 31st Dec 2018
 */

include("../connect/connect.php");
ini_set("display_errors",true);
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
	$contact_name = $_POST['contact_name'];
	$mobile_no= sanitize_string($_POST['mobile_no']);
	$account_no = sanitize_string($_POST['account_no']);
	
	//Validate phone no
if(!preg_match('^\d{12}(\d{2})?$^', $mobile_no)){
die("<div class='alert alert-danger'>* Enter a valid mobile phone no. Must be in the format <strong>254721123456</strong></div>");
}

try{
$conn->beginTransaction();
//Check if record exist
$confirm = $conn->prepare("SELECT id AS contact_id FROM contacts WHERE contact_name='".$contact_name."' AND mobile_no='".$mobile_no."' AND company_id='".$_SESSION['FON_G_COMPANY_ID']."' ");
$confirm->execute();
$count_confirm = $confirm->rowCount();

if($count_confirm>=1){
die("<div class='alert alert-danger'>* You already have this contact </div>");
}

//Lets save this record
$save = $conn->prepare("INSERT INTO contacts(contact_name, mobile_no,user_id,company_id,account_no,date_created)
VALUES('".$contact_name."','".$mobile_no."','".$_SESSION['FON_G_USER_ID']."','".$_SESSION['FON_G_COMPANY_ID']."','".$account_no."',now())");
$save->execute();
$row_count = $save->rowCount();
$conn->commit();
if($row_count>=1){
echo '<script>$("#form_content").empty();</script>';
echo "<div class='alert alert-success'><i class='fa fa-check-circle' ></i> New contact added!</div>";
echo '<script>
		 window.setTimeout(function(){
		// Move to a new location or you can do something else
		window.location.href = "?settings&action=new_contact";
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