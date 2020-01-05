<?php
	//require_once("utils".DIRECTORY_SEPARATOR."nameStringOrder.php");
	require_once("db".DIRECTORY_SEPARATOR."db.php");
	//use peterkahl\nameStringOrder\nameStringOrder;
	//global $mydb;

	class DataExtractor{
		function extract($file, $capid){
			global $mydb;
			$phone = "";
			$email = "";
			$fname = "";
			$lname = "";

			$handle = fopen($file, "r");
			if ($handle) {
			    while (($line = fgets($handle)) !== false) {
				    if(preg_match('/^(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}$/i',$line, $matches)){
				      	$phone = $matches[0];
				    }

				    if (preg_match("/[a-z\d._%+-]+@[a-z\d.-]+\.[a-z]{2,4}\b/i", $line, $email)){
				       	$email = $email[0];
				    }

				    /*$nameExtract = new nameStringOrder($line);
				    $fname = $nameExtract->getFirst();
				    $lname = $nameExtract->getLast();*/
				    $name = $this->search($line);
				    if (!is_null($name)){
					    $fname = $name[0];
					    $lname = $name[1];
					}

				    /*if (isset($phone) && isset($email) && isset($fname) && isset($lname)){ //leave out for now
				    	break;
				    }*/
			    }
			    
			    $newuserid = md5(uniqid(rand(), true));
			    $actid = md5(uniqid(rand(), true));

			    if (is_array($email)){
			    	$email = "";
			    }
			    //$path_parts = pathinfo($file);
				//$filename = $path_parts["filename"];
			    //$mydb->query("INSERT INTO captures(`CaptureID`, `UploadDate`, `UploadTime`, `UserID`, `FileID`) VALUES('$capid', CURDATE(), CURTIME(), '$userid', '$filename');");
			    $mydb->query("INSERT INTO account(`UserID`, `Role`, `CaptureID`, `FirstName`, `LastName`, `EmailAddress`, `PhoneNumber`) VALUES('$newuserid', 2, '$capid', '$fname', '$lname', '$email', '$phone');");
			    $mydb->query("INSERT INTO activation(`ActivationID`, `UserID`, `Activated`) VALUES('$actid', '$newuserid', 0);");
			    fclose($handle);
			} else {
			    throw new Exception("Error opening file: ".$file);
			} 
		}

		function search($str){
			require "utils".DIRECTORY_SEPARATOR."dictionary-first-names.php";
			$arr = explode(" ", $str);

			for ($i = 0; $i < count($arr); $i++){
				if (in_array(mb_strtolower($arr[$i]), $dict)){
					return array($arr[$i], $arr[$i+1]); //shouldn't have array out of bounds as first & last name should be on the same line
				}
			}
		}
	}
	//$dataextract = new DataExtractor();
?>