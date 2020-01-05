<?php
	session_start();
	require_once("db".DIRECTORY_SEPARATOR."db.php");
	//require_once("dataextract.php");
	$target_dir = "uploads".DIRECTORY_SEPARATOR;
	$target_file = $target_dir . basename($_FILES["image"]["name"]);
	$uploadOk = 1;
	$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
	$renameTo = md5(uniqid(rand(), true)).".".$imageFileType;
	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"])) {
	    $check = getimagesize($_FILES["image"]["tmp_name"]);
	    if($check !== false) {
	        echo "File is an image - " . $check["mime"] . ".";
	        $uploadOk = 1;
	    } else {
	        echo "File is not an image.";
	        $uploadOk = 0;
	    }
	}

	// Check file size
	if ($_FILES["image"]["size"] > 500000) {
	    echo "Sorry, your file is too large.";
	    $uploadOk = 0;
	}
	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
	&& $imageFileType != "gif" ) {
	    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
	    $uploadOk = 0;
	}
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
	    echo "Sorry, your file was not uploaded.";
	// if everything is ok, try to upload file
	} else {
	    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir.$renameTo)) {
	        echo "The file ". basename( $_FILES["image"]["name"]). " has been uploaded.";
	    } else {
	        echo "Sorry, there was an error uploading your file.";
	    }
	}
	$capid = md5(uniqid(rand(), true));
	//$userid = $_SESSION["userid"];
	//$mydb->query("INSERT INTO captures(`CaptureID`, `UploadDate`, `UploadTime`, `UserID`, `TxTFileName`, `ImgFileName`) VALUES($capid, CURDATE(), CURTIME(), $userid, $renameTo, $renameTo);");
	include("conversion.php");
	//include ("dataextract.php");
	$conversion = new ResumeConversion();
	$conversion->convert($target_dir.$renameTo);
	//$dataextract = new DataExtractor();
	//$dataextract->extract($target_dir.$renameTo, $capid);
?>