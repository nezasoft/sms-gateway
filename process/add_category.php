<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Add New Category
 * 21st Dec 2018
 */

include("../connect/connect.php");
ini_set("display_errors",true);
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
	$cat_name = sanitize_string($_POST['cat_name']);

try{
$conn->beginTransaction();
//Check if record exist
$confirm = $conn->prepare("SELECT id AS cat_id FROM client_category WHERE category_name='".$cat_name."' ");
$confirm->execute();
$count_confirm = $confirm->rowCount();

if($count_confirm>=1){
die("<div class='alert alert-danger'>* This category already exists in system.</div>");
}

//Lets save this record
$save = $conn->prepare("INSERT INTO client_category(category_name)VALUES('".$cat_name."')");
$save->execute();
$row_count = $save->rowCount();
$conn->commit();
if($row_count>=1){
echo '<script>$("#form_content").empty();</script>';
echo "<div class='alert alert-success'><i class='fa fa-check-circle' ></i> New category added!</div>";
echo '<script>
		 window.setTimeout(function(){
		// Move to a new location or you can do something else
		window.location.href = "?settings&action=new_category";
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