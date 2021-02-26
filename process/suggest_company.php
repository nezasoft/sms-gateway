<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Suggest Company
 * 18th Dec 2018
 */

include("../connect/connect.php");
ini_set("display_errors",true);
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
try{
	$keyword = str_replace("'","",sanitize_string($_POST['keyword']));
	$bold= '<strong><font color="#FF5733">'.$keyword.'</font></strong>';
	$query = $conn->prepare("SELECT company_name, id AS company_id, physical_address FROM company WHERE company_name LIKE '%".$keyword."%' ORDER BY company_name ASC LIMIT 30");
    $query->execute();   	
	$num_rows = $query->rowCount();
    $query_rows = $query->fetchAll(PDO::FETCH_ASSOC);
	$count=0;
	if($num_rows>=1){
	
		echo '<div class="alert alert-success">';
		foreach($query_rows as $query_row){
      $count++;
		echo '<div class="alert alert-success alert-st-one" role="alert">
				   <i class="fa fa-check edu-checked-pro admin-check-pro" aria-hidden="true"></i>
				   <p class="message-mg-rt"><strong>'.str_ireplace($keyword,$bold,titleCase($query_row["company_name"])).'</strong> '.str_ireplace($keyword,$bold,$query_row["physical_address"]).'</p>
			  </div>';
		}
		echo '</ul><br/><strong>'.$count.'</strong> record(s) found for <strong>'.$keyword.'</strong></div>';
		if($count>=30){
		echo "<div class='alert alert-info'> Search is limited to 30 records. Refine your search for better results</div>";
		}
	}else{
	echo "<div class='alert alert-danger' ><strong>".$keyword."</strong> not found in system</div>";
	}
}catch(PDOException $e){

die("<div class='alert alert-danger'>".$e->getMessage()."</div>"); 
}
}else{
die("<font color='red'>You are not authorized to access this content.</font>");
}
?>