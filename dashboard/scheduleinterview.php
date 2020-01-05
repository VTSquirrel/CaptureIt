<?php
	require_once("../db/db.php");
	$id = $_POST["id"];
	$status = $_POST["view-status"];
	$date = $_POST["int-date"];
	$start = $_POST["int-start"];
	$end = $_POST["int-end"];
	$round = $_POST["int-round"];
	$intid = md5(uniqid(rand(), true));
	$mydb->query("CALL ScheduleInterview('$id', $status, '$date', '$start', '$end', $round, TRUE, '$intid');");
?>