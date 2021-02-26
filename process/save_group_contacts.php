<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Save contacts to groups
 * 15th Dec 2018
 */

 //Connect to DB
 include("../connect/connect.php"); 
date_default_timezone_set("Africa/Nairobi");
ini_set("display_errors",true);

//check if its an ajax request
 if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {

 $group = str_replace("'","",sanitize_string($_POST['group']));
 $contacts = sanitize_string($_POST['contacts']);
 $contacts = explode(',',$contacts);
 $count = 0;
 
try{
$conn->beginTransaction(); 

//Lets save this contacts in the Db
foreach($contacts as $contact){
   	$count++; 
	
	//Confirm if this record already exists
	 $confirm=$conn->prepare("SELECT id FROM group_contacts WHERE contact_id='".$contact."' AND group_id='".$group."' LIMIT 1");
	 $confirm->execute();
	 $count_row=$confirm->rowCount();
	 
	 if($count_row==0){
      $save = $conn->prepare("INSERT INTO group_contacts(contact_id,group_id)VALUES('".$contact."','".$group."')");
	  $save->execute();
    }
	 
	
}


$conn->commit(); 
echo "<br/><div class='alert alert-success'><i class='fa fa-check-circle' ></i> <strong>".$count."</strong> Contacts added to group successfully!</div>";
}catch(PDOException $e){
$conn->rollBack();
die("<div class='alert alert-danger'>Database Error:- ".$e->getMessage()."</div>");
}
}else{
  die("<font color='red'>You are not authorized to access this content.</font>");
}	
?>

