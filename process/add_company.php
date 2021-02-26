<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Add New Company
 * 17th Dec 2018
 */

include("../connect/connect.php");
ini_set("display_errors",true);
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
try{
$conn->beginTransaction();
$company = sanitize_string($_POST['company_name']);
$description = sanitize_string($_POST['company_desc']);
$category = sanitize_string($_POST['category']);
$physical = sanitize_string($_POST['physical']);
$mobile_no = $_POST['mobile_no'];
$telephone_no = sanitize_string($_POST['telephone_no']);
$email = sanitize_string($_POST['email']);

//Validate email
if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
 die('<div class="alert alert-close alert-danger">
	<div class="alert-content">
           <h4 class="alert-title"> <i class="glyph-icon  icon-times" ></i>Email Error</h4>     
           <p>*Enter a valid email address</p>
       </div>
	  </div>');
	  exit();
}


//Validate phone no
if(!preg_match('^\d{12}(\d{2})?$^', $mobile_no)){
die("<div class='alert alert-danger'>* Enter a valid mobile phone no. Must be in the format <strong>254721123456</strong></div>");
}

//Confirm if record exists
$query = $conn->prepare("SELECT id FROM company WHERE company_name='".$company."' LIMIT 1");
$query->execute();
$confirm_rows = $query->rowCount();
if($confirm_rows==1){
die("<div class='alert alert-danger'>* Company already registered in the system.</div>");
}

//save data
$save = $conn->prepare("INSERT INTO company(company_name,company_desc,category_id,physical_address,email,mobile_no,tel_no,date_created)
VALUES('".$company."','".$description."','".$category."','".$physical."','".$email."','".$mobile_no."','".$telephone_no."',now())");
$save->execute();
$count_rows = $save->rowCount();

if($count_rows){
echo '<script>$("#form_content").empty();</script>';
echo "<div class='alert alert-success'> <i class='fa fa-check-circle' ></i> Company details saved! </div>";
echo '<script>
		 window.setTimeout(function(){
		// Move to a new location or you can do something else
		window.location.href = "?settings&action=new_company";
		}, 3000);
	  </script>';
}else{
echo "<div class='alert alert-danger'> <i class='fa fa-check-cirle' ></i> Error saving data. Contact system administrator </div>";
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