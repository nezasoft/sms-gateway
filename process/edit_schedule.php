<?php
ini_set("display_errors",true);
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Update schedules
 * @6th Sep 2019
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
  $active = sanitize_string($input['Active']);
  $start_date = sanitize_string($input['StartDate']);
  $end_date = sanitize_string($input['EndDate']);
  $time = sanitize_string($input['EndTime']);
  $schedule_name = sanitize_string($input['ScheduleName']);


   	if($input['action']==='edit'){
	 $query=$conn->prepare("UPDATE schedules SET schedule_name='".$schedule_name."',active='".$active."',start_date='".$start_date."',end_date='".$end_date."',schedule_time='".$time."' WHERE id='".$item_id."' LIMIT 1");
	 $query->execute();
	 $row_count = $query->rowCount();
	}elseif($input['action']==='delete'){
   
      $query = $conn->prepare("DELETE FROM schedules WHERE id='".$item_id."' LIMIT 1");
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
