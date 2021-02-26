<?php
/*Author Walter Omedo - Frontier Optical Networks Limited -- This application is used to create a template and a schedule for message
 * 9th Sept 2019
 */

 //Connect to DB
 include("../connect/connect.php"); 
date_default_timezone_set("Africa/Nairobi");
ini_set("display_errors",true);


//check if its an ajax request
 if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
$schedule = sanitize_string($_POST['schedule']);
$group = sanitize_string($_POST['group']);
$message = str_replace("'",'',sanitize_string($_POST['message_schedule']));
try{
$conn->beginTransaction();
//Check if this record already exists
$check = $conn->prepare("SELECT id FROM message_schedules WHERE company_id='".$_SESSION['FON_G_COMPANY_ID']."' AND group_id='".$group."' AND schedule_id='".$schedule."' LIMIT 1");
$check->execute();
$check_count = $check->rowCount();

if($check_count==1){
echo "<br/><div class='alert alert-danger'>*You have already generated a schedule for this message and group. Kindly remove the existing schedule if you want to update or extend the schedule</div>";
exit();

}



//Save this schedule
$save_sche = $conn->prepare("INSERT INTO  message_schedules(schedule_id,message,group_id,schedule_date,user_id,company_id)
VALUES('".$schedule."','".$message."','".$group."',curdate(),'".$_SESSION['FON_G_USER_ID']."','".$_SESSION['FON_G_COMPANY_ID']."')");
$save_sche->execute();

//Lets get the details of the schedule template ie hourly, daily, monthly, weekly
$get_schedules = $conn->prepare("SELECT s.id AS schedule_id,f.id AS frequency_id,start_date,end_date,schedule_time,f.frequency_name FROM schedules AS s LEFT JOIN frequency AS f ON f.id = s.frequency_id WHERE s.id='".$schedule."' LIMIT 1");
$get_schedules->execute();
$get_schedules_row = $get_schedules->fetch(PDO::FETCH_ASSOC);
$start_date = $get_schedules_row['start_date'];
$end_date = $get_schedules_row['end_date'];
$schedule_time = $get_schedules_row['schedule_time'];
$frequency = $get_schedules_row['frequency_id'];

//Save schedule
$save_schedule = $conn->prepare("INSERT INTO message_schedules(schedule_id,message,group_id,schedule_date,user_id,company_id)
VALUES('".$schedule."','".$message."','".$group."',curdate(),'".$_SESSION['FON_G_USER_ID']."','".$_SESSION['FON_G_COMPANY_ID']."')");
$save_schedule->execute();

//Get Message Schedule
$get_schedule = $conn->prepare("SELECT id AS message_schedule_id FROM message_schedules WHERE user_id='".$_SESSION['FON_G_USER_ID']."' AND company_id='".$_SESSION['FON_G_COMPANY_ID']."' AND message='".$message."' ORDER BY id DESC LIMIT 1");
$get_schedule->execute();
$get_schedule_row = $get_schedule->fetch(PDO::FETCH_ASSOC);

$message_schedule_id = $get_schedule_row['message_schedule_id'];


if($frequency==1){
$period = "+ 1 hour";

}elseif($frequency==2){
$period = "+ 1 day";

}elseif($frequency==3){
$period = "+ 1 week";
}elseif($frequency==4){
$period = "+ 1 month";
}
$date = $start_date;
//Lets generate an SMS sending schedule
	while (strtotime($date) <= strtotime($end_date)) {
			        $year = substr($date, 0,4);
				$d = date_parse_from_format("Y-m-d",$date);
				$month = $d["month"];//Get the month from the date
                                $date2 = date("Y-m-d",strtotime("+ 1 day",strtotime($date)));				
				if($frequency==1){
                                $ts1 = $date." ".$schedule_time;
                                $ts2 = $date2." ".$schedule_time;
                                $hours = abs(strtotime($ts1) - strtotime($ts2))/(60*60);
                                $time = strtotime($schedule_time);
                             
                                for($i=0;$i<=$hours;$i++){
                                 $time = date("H:i:s",strtotime($period,strtotime($time)));
                                  //Save  record
                                  $save = $conn->prepare("INSERT INTO scheduled_messages(message_schedule_id,schedule_date,schedule_time,status)
                                  VALUES('".$message_schedule_id."','".$date."','".$time."','Pending')");
                                  $save->execute();
                                }
                                
				
                        	$date = date ("Y-m-d", strtotime("+ 1 day", strtotime($date)));			
                               
				}else{
				$date = date ("Y-m-d", strtotime($period, strtotime($date)));
				
				
				}
				
	}
  $conn->commit();

  echo "<br/><div class='alert alert-success'>Message scheduled successfully!</div>";


}catch(PDOException $e){
	$conn->rollBack();
  die("<br/><div class='alert alert-danger'>Database Error:- ".$e->getMessage()."</div>");	
}
}else{
  die("<font color='red'>You are not authorized to access this content.</font>");
}	
?>



