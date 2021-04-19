<?php

	session_start();
	include('../db.php');
	include('../functions.php');

	// get GET parameters
	$unit_id = $_REQUEST["unitID"];
	$lector_id = $_REQUEST["lectorID"];
	$is_editor = $_REQUEST["is_editor"];
	
	// UPDATE
	$query = "UPDATE unit_account SET is_editor = $is_editor WHERE unit_id=$unit_id AND account_id = $lector_id";
		if (!($result = $mysqli->query($query))) {
			echo 'nevyslo to';
		} else {
			echo $is_editor;
			//echo $query;
		}

?>