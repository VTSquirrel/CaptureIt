<?php
	require_once("convertio".DIRECTORY_SEPARATOR."Convertio.php");
	require_once("db".DIRECTORY_SEPARATOR."db.php");
	use \Convertio\Convertio;
	class ResumeConversion{
		public function convert($target){
			global $mydb;
			//ce8870a20969c80f01dacbe17bf5fbc1
			//4d7c3dffb1ea24afe54911eff9df33c4
			$API = new Convertio("ce8870a20969c80f01dacbe17bf5fbc1");
			$path_parts = pathinfo($target);
			$filename = $path_parts["filename"];
			$file = "conversions/".$filename.".txt";
			$API->start($target, 'txt',
				[                                          
					'ocr_enabled' => true,
					'ocr_settings' => [
						'langs' => ['eng'],
						'page_nums' => '1'
					 ]
				]
				)
			->wait()                                          
			->download($file)                        
			->delete();

			if (!isset($_SESSION["upload-success"])){
				$_SESSION["upload-success"] = true;
			}
			$capid = md5(uniqid(rand(), true));
			$userid = $_SESSION["userid"];
			$mydb->query("CALL InsertCapture('$capid', '$userid', '$filename');");
			$this->extract($file, $capid);
		}

		private function extract($file, $capid){
			global $mydb;
			$phone = "";
			$email = "";
			$fname = "";
			$lname = "";

			$handle = fopen($file, "r");
			if ($handle) {
				$count = 0;
			    while (($line = fgets($handle)) !== false) {
			    	$count++;

			    	if (!empty($phone) && !empty($email) && !empty($fname) && !empty($lname)){
				    	break;
				    }

				    if(preg_match('/(\d[\s-]?)?[\(\[\s-]{0,2}?\d{3}[\)\]\s-]{0,2}?\d{3}[\s-]?\d{4}/i',$line, $matches)){
				      	$phone = $this->formatPhoneNumber($matches[0]);
				    }

				    if (preg_match("/[a-z\d._%+-]+@[a-z\d.-]+\.[a-z]{2,4}\b/i", $line, $match)){
				       	$email = trim($match[0], " ");
				    }

					$name = $this->search($line);
					if (count($name) > 0 && empty($fname) && empty($lname) && $count <= 3){
						$fname = trim($name[0], " ");
						$lname = trim($name[1], " ");
					}
			    }
			    
			    $newuserid = md5(uniqid(rand(), true));
			    $actid = md5(uniqid(rand(), true));
			    $statid = md5(uniqid(rand(), true));

			    $_SESSION["fname"] = $fname;
			    $_SESSION["lname"] = $lname;
			    $_SESSION["email"] = $email;
			    $_SESSION["phone"] = $phone;
			    $_SESSION["new-user-id"] = $newuserid;
			    $_SESSION["modal"] = true;
			    $mydb->query("CALL UploadCreateAccount('$newuserid', 2, '$capid', NULL, NULL, '$statid', '$fname', '$lname', '$email', '$phone', '$actid');");

			    #$body = "Hello ".$fname.", \n Thank you for visiting the CarMax booth! Please use the link below to complete your account and setup your applicant profile. \n".$_SERVER['SERVER_NAME']."register.php?id=".$actid."Best, \n The CarMax CaptureIt Team";
			    $body = file_get_contents("email/template.html");
			    $body = str_replace("[name]", $fname, $body);
			    $body = str_replace("[id]", $actid, $body);
			    $body = str_replace("[url]", "https://".$_SERVER['HTTP_HOST'], $body);
			    include("email/email.php");
			    $e = new Email();
			    $e->sendEmail($email, $fname." ".$lname, "Account Activation Code", $body);
			    fclose($handle);
			    //fclose($body);
			} else {
			    throw new Exception("Error opening file: ".$file);
			} 
		}
		
		private function search($str){
			require "utils".DIRECTORY_SEPARATOR."dictionary-first-names.php";
			$arr = explode(" ", $str);
			$first = "";
			$last = "";
			for ($i = 0; $i < count($arr); $i++){
				$no_utf = $this->remove_utf8_bom($arr[$i]);
				$closest = $this->getClosestName($no_utf);
				if (in_array(mb_strtolower($closest), $dict)){
					return array(ucfirst(mb_strtolower($no_utf)), ucfirst(mb_strtolower($arr[$i+1]))); //shouldn't have array out of bounds as first & last name should be on the same line
				}
			}
		}

		private function getClosestName($input){
			require "utils".DIRECTORY_SEPARATOR."dictionary-first-names.php";
			$words  = $dict;

			// no shortest distance found, yet
			$shortest = -1;
			$closest = "";

			// loop through words to find the closest
			foreach ($words as $word) {

			    // calculate the distance between the input word,
			    // and the current word
			    $lev = levenshtein($input, $word);

			    // check for an exact match
			    if ($lev == 0) {

			        // closest word is this one (exact match)
			        $closest = $word;
			        $shortest = 0;

			        // break out of the loop; we've found an exact match
			        break;
			    }

			    // if this distance is less than the next found shortest
			    // distance, OR if a next shortest word has not yet been found
			    if ($lev <= $shortest || $shortest < 0) {
			        // set the closest match, and shortest distance
			        $closest  = $word;
			        $shortest = $lev;
			    }
			}
			return $closest;
		}
		private function remove_utf8_bom($text){
		    $bom = pack('H*','EFBBBF');
		    $text = preg_replace("/^$bom/", '', $text);
		    return $text;
		}

		/* Courtesy of https://stackoverflow.com/a/14167216 */
		private function formatPhoneNumber($phoneNumber) {
			$phoneNumber = preg_replace('/[^0-9]/','',$phoneNumber);

			if(strlen($phoneNumber) > 10) {
				$countryCode = substr($phoneNumber, 0, strlen($phoneNumber)-10);
				$areaCode = substr($phoneNumber, -10, 3);
				$nextThree = substr($phoneNumber, -7, 3);
				$lastFour = substr($phoneNumber, -4, 4);

				$phoneNumber = '+'.$countryCode.' ('.$areaCode.') '.$nextThree.'-'.$lastFour;
			}
			else if(strlen($phoneNumber) == 10) {
				$areaCode = substr($phoneNumber, 0, 3);
				$nextThree = substr($phoneNumber, 3, 3);
				$lastFour = substr($phoneNumber, 6, 4);

				$phoneNumber = '('.$areaCode.') '.$nextThree.'-'.$lastFour;
			}
			else if(strlen($phoneNumber) == 7) {
				$nextThree = substr($phoneNumber, 0, 3);
				$lastFour = substr($phoneNumber, 3, 4);

				$phoneNumber = $nextThree.'-'.$lastFour;
			}

			return $phoneNumber;
		}
	}
?>