<?php

include("../connect/connect.php");
if(isset($_GET['logout'])){
session_unset();
session_destroy();

echo "<script>window.location.href='../';</script>";
}

?>