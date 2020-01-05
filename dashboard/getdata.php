<?php
	require_once("../db/db.php");
	$result = $mydb->query("SELECT CONCAT(a.FirstName, ' ', a.LastName) AS name, a.EmailAddress, a.PhoneNumber, ap.GPAOverall, ap.GPAInMajor, s.SchoolName, sc.StatusCode, sc.StatusTitle, r.Note, i.Date, i.Start, i.End, i.Round FROM account a LEFT JOIN applicant_profile ap ON a.ProfileID = ap.ProfileID LEFT JOIN schools s ON ap.SchoolID = s.SchoolID LEFT JOIN applicant_status stat ON a.StatusID = stat.StatusID LEFT JOIN status_code sc ON stat.Status = sc.StatusCode LEFT JOIN resume_notes r ON a.NoteID = r.NoteID LEFT JOIN interviews i ON i.InterviewID = a.InterviewID WHERE a.UserID='".$_POST["UserID"]."';");
	$row = mysqli_fetch_array($result);
	$email = $row["EmailAddress"];
	$phone = $row["PhoneNumber"];
	$school = $row["SchoolName"];
	$code = $row["StatusCode"];
	$stat = $row["StatusTitle"];
	$gpao = $row["GPAOverall"];
	$gpai = $row["GPAInMajor"];
	$name = $row["name"];
	$note = $row["Note"];
	$date = $row["Date"];
	$start = $row["Start"];
	$end = $row["End"];
	$round = $row["Round"];
	echo json_encode(array($email, $phone, $school, $stat, $gpao, $gpai, $name, $note, $date, $start, $end, $round, $code));
?>