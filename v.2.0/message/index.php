<?php
header("Content-Type:application/json");
if (!$request=file_get_contents('php://input')){
echo "Invalid input";
exit();
}


file_put_contents('log.txt',$request,FILE_APPEND | LOCK_EX);
file_put_contents('log.txt', "\n", FILE_APPEND);

?>
