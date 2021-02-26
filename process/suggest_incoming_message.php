<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Suggest Incoming Message
 * 12th Jan 2019
 */

include("../connect/connect.php");
ini_set("display_errors",true);
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
if($_SESSION['FON_G_COMPANY_ID']==2 && $_SESSION['FON_G_LEVEL_ID']==4){ 
$where = "AND om.company_id LIKE '%%'";
}else{
$where = "AND om.company_id ='".$_SESSION['FON_G_COMPANY_ID']."'";
}
try{
	$keyword = str_replace("'","",sanitize_string($_POST['keyword']));
	$bold= '<strong><font color="#FF5733">'.$keyword.'</font></strong>';
	$query = $conn->prepare("SELECT om.id AS message_id,om.mobile_no, om.message,om.msg_time,om.msg_date
		   								 FROM incoming_messages AS om 
                                  WHERE  (om.mobile_no LIKE '%".$keyword."%' OR om.message LIKE '%".$keyword."%') ".$where."
                                  ORDER BY om.id DESC LIMIT 30");
   $query->execute();   	
	$num_rows = $query->rowCount();
    $query_rows = $query->fetchAll(PDO::FETCH_ASSOC);
	$count=0;
	if($num_rows>=1){	
		echo '<br/><div class="alert alert-success">';
		foreach($query_rows as $query_row){
      $count++;
		echo '<div class="alert alert-success alert-st-one" role="alert">
				   <i class="fa fa-check edu-checked-pro admin-check-pro" aria-hidden="true"></i>
				   <p class="message-mg-rt">Mobile No: <strong>'.str_ireplace($keyword,$bold,$query_row["mobile_no"]).'</strong>  Message: '.str_ireplace($keyword,$bold,$query_row['message']).' Date & Time: <strong> '.str_ireplace($keyword,$bold,$query_row['msg_date']).' '.str_ireplace($keyword,$bold,$query_row['msg_time']).'</strong></p>
			  </div>';
		}
		echo '</ul><br/><strong>'.$count.'</strong> record(s) found for <strong>'.$keyword.'</strong></div>';
		if($count>=30){
		echo "<div class='alert alert-info'> Search is limited to 30 records. Refine your search for better results</div>";
		}
	}else{
	echo "<br/><div class='alert alert-danger' ><strong>".$keyword."</strong> not found in sent messages</div>";
	}
}catch(PDOException $e){

die("<div class='alert alert-danger'>".$e->getMessage()."</div>"); 
}
}else{
die("<font color='red'>You are not authorized to access this content.</font>");
}
?>