<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited --Import Contacts
 * 12th Jan 2019
 */

include("../connect/connect.php");
ini_set("display_errors",false);
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
	
	
	
	if(empty($_FILES['import_contacts']['name'])){
	die("<div class='alert alert-danger'>* Please choose file to upload!</div>");
	}
	$allowed_ext = array('csv');
	$extension = end(explode(".",$_FILES['import_contacts']['name']));
	
	if(!in_array($extension,$allowed_ext)){
	die("<div class='alert alert-danger'>* Invalid file. Only CSV files allowed!</div>");
	}
	
	$file_data = fopen($_FILES['import_contacts']['tmp_name'],'r');
	fgetcsv($file_data);
	try{
	$conn->beginTransaction();
	while($row =fgetcsv($file_data)){
		$name = $row[0];
		$mobile_no = $row[1];
		$account_no = $row[2];
		
		//Check if record exist
		$confirm = $conn->prepare("SELECT id AS contact_id FROM contacts WHERE mobile_no='".$mobile_no."'  AND company_id='".$_SESSION['FON_G_COMPANY_ID']."' LIMIT 1");
		$confirm->execute();
		$count_confirm = $confirm->rowCount();

		if($count_confirm>=1){
		//Skip this record
		}else{
			//Before we save lets check the formar of the mobile no must have a country code prefix 
			//in the format +254721123456
			if(!preg_match('^\d{12}(\d{2})?$^', $mobile_no)){
				//Skip this record
			}else{
			 //Save this record
			//Lets save this record
			$save = $conn->prepare("INSERT INTO contacts(contact_name,mobile_no,account_no,user_id,company_id,date_created)
			VALUES('".$name."','".$mobile_no."','".$account_no."','".$_SESSION['FON_G_USER_ID']."','".$_SESSION['FON_G_COMPANY_ID']."',curdate())");
			$save->execute();
			
			}
		
		
		}
	
	}
$conn->commit();
echo '<script>
$(".modal-body").empty();
</script>';
echo "<div class='alert alert-success'><i class='fa fa-check-circle' ></i> Contacts imported successfully!</div>"; 

}catch(PDOException $e){
$conn->rollBack();
 die("<div class='alert alert-danger'>Database Error:- ".$e->getMessage()."</div>");
}

}else{
die("<font color='red'>You are not authorized to access this content.</font>");
}
?>