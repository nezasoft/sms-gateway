<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Add New Short Code
 * 26th Dec 2018
 */

include("../connect/connect.php");
ini_set("display_errors",true);
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
	$category = sanitize_string($_POST['category']);
	$short_code = sanitize_string($_POST['short_code']);
	$desc = sanitize_string($_POST['desc']);
	$service = sanitize_string($_POST['service']);
	$product = sanitize_string($_POST['product']);

try{
$conn->beginTransaction();
//Check if record exist
$confirm = $conn->prepare("SELECT id AS short_id FROM short_codes WHERE short_code='".$short_code."' AND service_id='".$service."' ");
$confirm->execute();
$count_confirm = $confirm->rowCount();

if($count_confirm>=1){
die("<div class='alert alert-danger'>* Another service has already been registered to use this short code</div>");
}

//Lets save this record
$save = $conn->prepare("INSERT INTO short_codes(category_id,short_code,short_code_description,date_created, status,service_id,product_id)
VALUES('".$category."','".$short_code."','".$desc."',now(),'0','".$service."','".$product."')");
$save->execute();
$row_count = $save->rowCount();
$conn->commit();
if($row_count>=1){
echo '<script>$("#form_content").empty();</script>';
echo "<div class='alert alert-success'><i class='fa fa-check-circle' ></i> New short code and service registered!</div>";
echo '<script>
		 window.setTimeout(function(){
		// Move to a new location or you can do something else
		window.location.href = "?settings&action=new_short_code";
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
