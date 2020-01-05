<?php
	require_once("../db/db.php");
	$result = $mydb->query("CALL GetInterviews();");

	class Event{}
	$events = array();

	while ($row = mysqli_fetch_array($result)){
		$e = new Event();
		$e->title = $row["name"]." (Round ".$row["InterviewRound"].")";
		$e->start = $row["InterviewDate"]."T".$row["InterviewStart"];
		$e->end = $row["InterviewDate"]."T".$row["InterviewEnd"];
		
		$events[] = $e;
	}
	echo json_encode($events);
?>