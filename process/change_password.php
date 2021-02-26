<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Change Password
 * 13th Jan 2019
 */

include("../connect/connect.php");
ini_set("display_errors",true);
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
	$current_password = $_POST['current_password'];
   $confirm_password = $_POST['confirm_password'];
try{
$conn->beginTransaction();
//Lets check if the password provisioned matches the one in the DB
$confirm = $conn->prepare("SELECT id AS user_id FROM users WHERE id='".$_SESSION['FON_G_USER_ID']."' AND password='".create_pass_hash($current_password)."' LIMIT 1 ");
$confirm->execute();
$count_confirm = $confirm->rowCount();

if($count_confirm==0){
die("<div class='alert alert-danger'>* The current password provided does not match the one in our system. Check again!</div>");
}

//Lets save this record
$save = $conn->prepare("UPDATE users SET password='".create_pass_hash($confirm_password)."' WHERE id='".$_SESSION['FON_G_USER_ID']."' LIMIT 1");
$save->execute();
$row_count = $save->rowCount();
$conn->commit();
echo "<div class='alert alert-success'><i class='fa fa-check-circle' ></i> Password updated</div>";
echo '<script>
		 window.setTimeout(function(){
		// Move to a new location or you can do something else
		window.location.href = "?settings&action=update_password";
		}, 3000);
	  </script>';	  

}catch(PDOException $e){
$conn->rollBack();
 die("<div class='alert alert-danger'>Database Error:- ".$e->getMessage()."</div>");
}

}else{
die("<font color='red'>You are not authorized to access this content.</font>");
}
?>