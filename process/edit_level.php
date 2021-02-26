<?php
ini_set("display_errors",true);
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Update Levels
 * @21st Dec 2018
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
  $level_name = sanitize_string($input['LevelName']);


   	if($input['action']==='edit'){
	 $query=$conn->prepare("UPDATE levels SET level_name='".$level_name."' WHERE id='".$item_id."' LIMIT 1");
	 $query->execute();
	 $row_count = $query->rowCount();
	}elseif($input['action']==='delete'){
   
      $query = $conn->prepare("DELETE FROM levels WHERE id='".$item_id."' LIMIT 1");
      $query->execute();
      $row_count=$query->rowCount(); 
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