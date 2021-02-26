<?php
ini_set("display_errors",true);
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Update Short Codes
 * @26th Dec 2018
 */
//Make a connection
include("../connect/connect.php");

//check if its an ajax request
  if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
  try{
  $conn->beginTransaction();
  header('Content-Type: application/json');
  $input = filter_input_array(INPUT_POST);
  $item_id = sanitize_string($input['ItemID']);
  $short_code = sanitize_string($input['ShortCode']);
  $service = sanitize_string($input['ServiceID']);
  $product= sanitize_string($input['Product']);
  $category = sanitize_string($input['Category']);
  $desc = sanitize_string($input['Description']);
  $status = sanitize_string($input['Status']);

   if($input['action']==='edit'){
	 $query=$conn->prepare("UPDATE short_codes SET short_code='".$short_code."', service_id='".$service."', product_id='".$product."',category_id='".$category."',status='".$status."',short_code_description='".$desc."' WHERE id='".$item_id."' LIMIT 1");
	 $query->execute();
	 $row_count = $query->rowCount();
   $resp ='Updated!';
	}elseif($input['action']==='delete'){
   
      $query = $conn->prepare("DELETE FROM short_codes WHERE id='".$item_id."' LIMIT 1");
      $query->execute();
      $row_count=$query->rowCount(); 
      $resp='Deleted!';
	}	

	if($row_count>=1){
		echo json_encode($resp);
	}else{
		$err = 'Error occurred while trying to update record!';
        echo json_encode($err);
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