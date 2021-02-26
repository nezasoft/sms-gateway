<?php
ini_set("display_errors",true);
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Update Company Information
 * @19th Dec 2018
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
  $item_name = sanitize_string($input['CompanyName']);
  $category = sanitize_string($input['Category']);
  $email = sanitize_string($input['Email']);
  $mobile_no = sanitize_string($input['MobileNo']);
   	if($input['action']==='edit'){
	 $query=$conn->prepare("UPDATE company SET company_name='".$item_name."',category_id='".$category."',email='".$email."',mobile_no='".$mobile_no."' WHERE id='".$item_id."' LIMIT 1");
	 $query->execute();
	 $row_count = $query->rowCount();
	}elseif($input['action']==='delete'){
   /*
      $query = $conn->prepare("DELETE FROM company WHERE id='".$item_id."' LIMIT 1");
      $query->execute();
      $row_count=$query->rowCount();*/	  
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