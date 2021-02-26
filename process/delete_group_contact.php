<?php
/**
 * @author Walter Omedo - Frontier Optical Networks Limited -- Delete Group Contact
 * 16th July 2019
 */

include("../connect/connect.php");
ini_set("display_errors",true);
//check if its an ajax request
if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){

$id = $_POST['group_contact'];
$delete = $conn->prepare("DELETE FROM group_contacts WHERE id='".$id."' LIMIT 1");
$delete->execute();


}else{
die("<font color='red'>You are not authorized to access this content.</font>");
}

?>
