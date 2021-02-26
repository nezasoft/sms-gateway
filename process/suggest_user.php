<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Suggest Company
 * 18th Dec 2018
 */

include("../connect/connect.php");
ini_set("display_errors",true);
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
	
if($_SESSION['FON_G_COMPANY_ID']==2 && $_SESSION['FON_G_LEVEL_ID']==4){ 
$where = "AND c.id LIKE '%%'";
}else{
$where = "AND c.id ='".$_SESSION['FON_G_COMPANY_ID']."'";
}
try{
	$keyword = str_replace("'","",sanitize_string($_POST['keyword']));
	$bold= '<strong><font color="#FF5733">'.$keyword.'</font></strong>';
	$query = $conn->prepare("SELECT  us.id AS user_id, us.fname, us.lname, us.email, DATE_FORMAT(us.date_created,'%d %c %Y') AS date_created, us.last_login, us.active, c.company_name, l.level_name  FROM users AS us LEFT JOIN company AS c ON c.id = us.company_id LEFT JOIN levels as l ON l.id = us.level_id 
	WHERE (us.fname LIKE '%".$keyword."%' OR us.lname LIKE '%".$keyword."%') ".$where." ORDER BY us.fname ASC LIMIT 30");
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
				   <p class="message-mg-rt"><strong>'.titleCase($query_row["fname"]).' '.titleCase($query_row["lname"]).'</strong>  '.titleCase($query_row['company_name']).' '.$query_row['level_name'].'</p>
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