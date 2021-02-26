<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited --Import Wards
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
		$ward_name = str_replace("'","", $row[0]);
		
		//Check if record exist
		$confirm = $conn->prepare("SELECT id AS county_id FROM county  WHERE county_name='".$ward_name."' LIMIT 1");
		$confirm->execute();
		$count_confirm = $confirm->rowCount();

		if($count_confirm>=1){
		//Skip this record
		}else{
			 
			
				
			//Lets save this record
			$save = $conn->prepare("INSERT INTO county(county_name)VALUES('".$ward_name."')");
			$save->execute();
			
		
		

		}
	
	}
$conn->commit();
echo '<script>
$(".modal-body").empty();
</script>';
echo "<div class='alert alert-success'><i class='fa fa-check-circle' ></i> Data imported successfully!</div>"; 

}catch(PDOException $e){
$conn->rollBack();
 die("<div class='alert alert-danger'>Database Error:- ".$e->getMessage()."</div>");
}

}else{
die("<font color='red'>You are not authorized to access this content.</font>");
}
?>
