<?php

	session_start();
	include('../db.php');
	include('../functions.php');

	// get GET parameters
	$unit_id = $_REQUEST["unitID"];
	$lector_id = $_REQUEST["lectorID"];
	
	// UPDATE
	$query = "DELETE FROM unit_account WHERE unit_id=$unit_id AND account_id = $lector_id";
		if (!($result = $mysqli->query($query))) {
			echo 'nevyslo to';
		} else {
			echo 'true';
		}
