<?php
 /*Walter Omedo - Frontier Optical Networks Limited -- Update user password
 * 9th Apr 2019
 */

include("../connect/connect.php");
ini_set("display_errors",true);
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
$password=str_replace("'","",sanitize_string($_POST['confirm_password']));
$email = sanitize_string($_POST['email']);
$token = sanitize_string($_POST['token']);
try{
$conn->beginTransaction();
//Check account status
$check_status = $conn->prepare("SELECT id FROM users WHERE email='".$email."' AND active='0' LIMIT 1");
$check_status->execute();
$check_status_count = $check_status->rowCount();
if($check_status_count>=1){
die("<div class='alert alert-danger'>Your account is inactive. Contact your administrator </div>");
}

//Lets update user password
$update_password = $conn->prepare("UPDATE users SET password='".create_pass_hash($password)."', update_pass='Yes' WHERE email='".$email."' AND token='".$token."' LIMIT 1");
$update_password->execute();
$num_rows = $update_password->rowCount();

if($num_rows>=1){
//Try to login
$check_login = $conn->prepare("SELECT id AS user_id,company_id,fname,level_id FROM users WHERE email='".$email."' AND password='".create_pass_hash($password)."'  LIMIT 1");
$check_login->execute();
$check_login_count=$check_login->rowCount();
}else{
die("<div class='alert alert-danger'>Update password request fail. Try again!</div>");
}

if($check_login_count==0){
die("<div class='alert alert-danger'>Invalid username/email or password!</div>");
}

$user_row = $check_login->fetch(PDO::FETCH_ASSOC);
$_SESSION['FON_G_USER_ID'] = $user_row['user_id'];
$_SESSION['FON_G_FNAME'] = $user_row['fname'];
$_SESSION['FON_G_COMPANY_ID'] = $user_row['company_id'];
$_SESSION['FON_G_LEVEL_ID'] = $user_row['level_id'];

if($_SESSION['FON_G_USER_ID']!=''){
//Update last login
$update=$conn->prepare("UPDATE users SET last_login=now() WHERE id='".$_SESSION['FON_G_USER_ID']."' LIMIT 1");
$update->execute();
echo '<script>$("#form_content").empty();</script>';
echo "<div class='alert alert-success'><i class='fa fa-check-circle' ></i> Password update successful!</div>";
echo '<script>
		 window.setTimeout(function(){
		// Move to a new location or you can do something else
		window.location.href = "../my_account?settings&action=list_contacts";
		}, 2000);
	  </script>';
}
$conn->commit();
}catch(PDOException $e){
$conn->rollBack();
 die("<div class='alert alert-danger'>Database Error:- ".$e->getMessage()."</div>");
}

}else{
die("<font color='red'>You are not authorized to access this content.</font>");
}
?>
