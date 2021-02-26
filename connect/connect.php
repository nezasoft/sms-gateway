
<?php
exit();
ini_set('display_errors',false);
/**
 * @author Walter Omedo - Frontier Optical Networks Limited
 * @copyright 2018
 */
$db_prefix='';
//Establish connection to server via PDO Object
	try{
		$conn = new PDO('mysql:host=localhost;dbname=fonsms;charset=utf8', 'root', '');
		$conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
		$conn->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
		if(!$conn){
			die('Error connecting to DB Server. Check your credentials');
		}else{
			session_start();
      //Lets remove empty messages
      $query = $conn->prepare("DELETE  FROM incoming_messages WHERE keyword IS NULL");
      $query->execute();
			function sanitize_string($var){
				$var= strip_tags($var);
				$var=htmlentities($var);
				$var=stripslashes($var);
                                //$var = mysql_real_escape_string($var);
				return $var;
			}
  
			// function to hash all saved passwords in databases
			function create_pass_hash($password) {
				global $conn;
				return hash_hmac('sha256',$password, 'c#haRl891', true);
			}
      function TruncateText($string, $limit, $break=".", $pad="...")
    {
    // return with no change if string is shorter than $limit
    if(strlen($string) <= $limit) return $string;
      // is $break present between $limit and the end of the string?
      if(false !== ($breakpoint = strpos($string, $break, $limit))) {
      if($breakpoint < strlen($string) - 1) {
        $string = substr($string, 0, $breakpoint) . $pad;
      }
    }

     return $string;
    }
			//Convert to title Case
			function titleCase($string, $delimiters = array(" ", "-", ".", "'", "O'", "Mc"), $exceptions = array("and", "to", "of", "das", "dos", "I", "II", "III", "IV", "V", "VI"))
					{
						/*
						 * Exceptions in lower case are words you don't want converted
						 * Exceptions all in upper case are any words you don't want converted to title case
						 *   but should be converted to upper case, e.g.:
						 *   king henry viii or king henry Viii should be King Henry VIII
						 */
						$string = mb_convert_case($string, MB_CASE_TITLE, "UTF-8");
						foreach ($delimiters as $dlnr => $delimiter) {
							$words = explode($delimiter, $string);
							$newwords = array();
							foreach ($words as $wordnr => $word) {
								if (in_array(mb_strtoupper($word, "UTF-8"), $exceptions)) {
									// check exceptions list for any words that should be in upper case
									$word = mb_strtoupper($word, "UTF-8");
								} elseif (in_array(mb_strtolower($word, "UTF-8"), $exceptions)) {
									// check exceptions list for any words that should be in upper case
									$word = mb_strtolower($word, "UTF-8");
								} elseif (!in_array($word, $exceptions)) {
									// convert to uppercase (non-utf8 only)
									$word = ucfirst($word);
								}
								array_push($newwords, $word);
							}
							$string = join($delimiter, $newwords);
					   }//foreach
					   return $string;
					}
			//Get all the values between two dates
            function createDateRangeArray($strDateFrom,$strDateTo)
			{
				// takes two dates formatted as YYYY-MM-DD and creates an
				// inclusive array of the dates between the from and to dates.

				// could test validity of dates here but I'm already doing
				// that in the main script

				$aryRange=array();

				$iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2), substr($strDateFrom,8,2),substr($strDateFrom,0,4));
				$iDateTo=mktime(1,0,0,substr($strDateTo,5,2), substr($strDateTo,8,2),substr($strDateTo,0,4));

				if ($iDateTo>=$iDateFrom)
				{
					array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
					while ($iDateFrom<$iDateTo)
					{
						$iDateFrom+=86400; // add 24 hours
						array_push($aryRange,date('Y-m-d',$iDateFrom));
					}
				}
				return $aryRange;
			}			
			//Lets expire sessions after a period of inactivity
			//Start our session.
			//Expire the session if user is inactive for 60
			//minutes or more.
			$expireAfter = 60;			 
			//Check to see if our "last action" session
			//variable has been set.
			if(isset($_SESSION['FON_LAST_ACTION'])){			
				//Figure out how many seconds have passed
				//since the user was last active.
				$secondsInactive = time() - $_SESSION['FON_LAST_ACTION'];				
				//Convert our minutes into seconds.
				$expireAfterSeconds = $expireAfter * 60;				
				//Check to see if they have been inactive for too long.
				if($secondsInactive >= $expireAfterSeconds){
					//User has been inactive for too long.
					//Kill their session.
					session_unset();
					session_destroy();
          				
					
				}				
			}			 
			//Assign the current timestamp as the user's
			//latest activity
			$_SESSION['FON_LAST_ACTION'] = time();
		}
	}catch(PDOException $e){
		echo '<title>Connection Error!!!</title>';
		die("<font color='red'>Error connecting to database:</font>".$e->getMessage());

	}
?>



