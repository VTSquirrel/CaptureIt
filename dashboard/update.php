<?php
	session_start();
	require_once("../db/db.php");
	if(isset($_POST["updateInfo"])){
		$addid = md5(uniqid(rand(), true));
		$userid = $_SESSION["userid"];
		$email = trim($_POST["email"], " ");
		$phone = trim($_POST["phone"], " ");
		$street = trim($_POST["address"], " ");
		$city = trim($_POST["city"], " ");
		$state = trim($_POST["state"], " ");
		$zip = trim($_POST["zip"], " ");
		$mydb->query("CALL UpdateContactInfo('$userid', '$addid', '$email', '$phone', '$street', '$city', '$state', '$zip');");
		$_SESSION["contact-update"] = true;
		Header("Location:./");
	}
	else if (isset($_POST["updatePass"])){
		if ($_POST["pass1"] != "" && $_POST["pass2"] != ""){
			if ($_POST["pass1"] == $_POST["pass2"]){
				$mydb->query("CALL UpdatePassword('".password_hash($_POST["pass1"], PASSWORD_DEFAULT)."', '".$_SESSION["userid"]."');");
				$_SESSION["password-update"] = true;
			}else{
				$_SESSION["password-update"] = false;
			}
		}else{
			$_SESSION["password-update"] = false;
		}
		Header("Location:./");
	}
	else if (isset($_POST["appdata"])){
		$dataid = md5(uniqid(rand(), true));
		$userid = $_SESSION["userid"];
		$school = $_POST["school"];
		$degree = $_POST["degtype"];
        $major = $_POST["major"];
        $minor = $_POST["minor"];
        $gdate = $_POST["gdate"];
        $currs = trim($_POST["extra"], " ");
        $ogpa = $_POST["o-gpa"];
        $igpa = $_POST["i-gpa"];
        $emptype = $_POST["emp-type"];
        $citizen = $_POST["citizenship"];

		$mydb->query("CALL UpdateAppData('$userid', '$dataid', '$school', '$ogpa', '$igpa', '$major', '$minor', '$degree', '$gdate', '$emptype', '$citizen', '$currs');");
		$_SESSION["profile-update"] = true;
		Header("Location:./");
	}
?>