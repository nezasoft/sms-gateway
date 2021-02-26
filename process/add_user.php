<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Add New User
 * 20th Dec 2018
 */

include("../connect/connect.php");
ini_set("display_errors",true);
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
	$company = sanitize_string($_POST['company']);
	$fname = sanitize_string($_POST['fname']);
	$lname = sanitize_string($_POST['lname']);
	$email = sanitize_string($_POST['email']);
	$password = $_POST['password'];
	$level = sanitize_string($_POST['level']);
try{
$conn->beginTransaction();
//Check if user exist
$confirm = $conn->prepare("SELECT id AS user_id FROM users WHERE email='".$email."' ");
$confirm->execute();
$count_confirm = $confirm->rowCount();

if($count_confirm>=1){
die("<div class='alert alert-danger'>* The email supplied is associated with another account. Try another one</div>");
}

//Lets save this record
$save = $conn->prepare("INSERT INTO users(company_id,fname,lname,email,password,date_created,level_id)
VALUES('".$company."','".$fname."','".$lname."','".$email."','".create_pass_hash($password)."',now(),'".$level."')");
$save->execute();
$row_count = $save->rowCount();
$conn->commit();
if($row_count>=1){
echo '<script>$("#form_content").empty();</script>';
echo "<div class='alert alert-success'><i class='fa fa-check-circle' ></i> User account created successfully!</div>";
echo '<script>
		 window.setTimeout(function(){
		// Move to a new location or you can do something else
		window.location.href = "?settings&action=new_user";
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