<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Add New Group
 * 21st Dec 2018
 */

include("../connect/connect.php");
ini_set("display_errors",true);
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
	$group_name = sanitize_string($_POST['group_name']);

try{
$conn->beginTransaction();
//Check if record exist
$confirm = $conn->prepare("SELECT id AS group_id FROM groups WHERE group_name='".$group_name."' AND company_id='".$_SESSION['FON_G_COMPANY_ID']."' ");
$confirm->execute();
$count_confirm = $confirm->rowCount();

if($count_confirm>=1){
die("<div class='alert alert-danger'>* This group already exists in system.</div>");
}

//Lets save this record
$save = $conn->prepare("INSERT INTO groups(group_name,company_id)VALUES('".$group_name."','".$_SESSION['FON_G_COMPANY_ID']."')");
$save->execute();
$row_count = $save->rowCount();
$conn->commit();
if($row_count>=1){
echo '<script>$("#form_content").empty();</script>';
echo "<div class='alert alert-success'><i class='fa fa-check-circle' ></i> New group added!</div>";
echo '<script>
		 window.setTimeout(function(){
		// Move to a new location or you can do something else
		window.location.href = "?settings&action=new_group";
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
