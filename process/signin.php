<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Signin
 * 21st Dec 2018
 */

include("../connect/connect.php");
ini_set("display_errors",true);
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
$email = sanitize_string($_POST['email']);
$password = $_POST['password'];

try{
//$conn->beginTransaction();
//Check if password has been updated
$confirm_update = $conn->prepare("SELECT update_pass,token FROM users WHERE email='".$email."' AND update_pass='No' LIMIT 1");
$confirm_update->execute();
$confirm_count = $confirm_update->rowCount();
$confirm_row = $confirm_update->fetch(PDO::FETCH_ASSOC);
$token = $confirm_row['token'];
$update_pass = $confirm_row['update_pass'];
if($confirm_count>=1){
echo '<script>$("#form_content").empty();</script>';
echo "<div class='alert alert-warning'><strong>Security check</strong> We need to update your password. Please wait...</div>";
die('<script>window.setTimeout(function(){window.location.href="update_pass?email='.$email.'&token='.$token.'"},2000);</script>');

}

//Check account status
$check_status = $conn->prepare("SELECT id FROM users WHERE email='".$email."' AND active='0' LIMIT 1");
$check_status->execute();
$check_status_count = $check_status->rowCount();
if($check_status_count>=1){
die("<div class='alert alert-danger'>Your account is inactive. Contact your administrator </div>");
}
//echo $password;
//$hash = create_pass_hash($password);
//echo '$hash';
//AND password='".create_pass_hash($password)."'
//Try to login
$check_login = $conn->prepare("SELECT id AS user_id,company_id,fname,level_id FROM users WHERE email='".$email."' AND password='".create_pass_hash($password)."' LIMIT 1");
$check_login->execute();
$check_login_count=$check_login->rowCount();

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
echo "<div class='alert alert-success'><i class='fa fa-check-circle' ></i> Authentication successful!</div>";
echo '<script>
		 window.setTimeout(function(){
		// Move to a new location or you can do something else
		window.location.href = "my_account?settings&action=list_contacts";
		}, 2000);
	  </script>';
}
//$conn->commit();
}catch(PDOException $e){
//$conn->rollBack();
 die("<div class='alert alert-danger'>Database Error:- ".$e->getMessage()."</div>");
}

}else{
die("<font color='red'>You are not authorized to access this content.</font>");
}
?>
