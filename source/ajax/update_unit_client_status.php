<?php

	session_start();
	include('../db.php');
	include('../functions.php');

	// get GET parameters
	$unit_id = $_REQUEST["unitID"];
	$client_id = $_REQUEST["clientID"];
	$status = $_REQUEST["status"];
	
	$query = '';
	if ($status == 'leave') {
		$query = "UPDATE unit_client SET date_leave=NOW() WHERE unit_id=$unit_id AND client_id = $client_id";
	} else {
		$query = "UPDATE unit_client SET status = '$status' WHERE unit_id=$unit_id AND client_id = $client_id";
	}
		
	if (!($result = $mysqli->query($query))) {
		echo 'nevyslo to';
	} else {
		echo '';
		//echo $query;
	}

?>