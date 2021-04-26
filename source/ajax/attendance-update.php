<?php

	session_start();
	include('../db.php');
	include('../functions.php');

	// get GET parameters
	$record_id = $_REQUEST["id"];
	$present = ($_REQUEST["value"] == 'true' ? '1' : '0');
	
	// UPDATE
	$query = "UPDATE unit_client SET present=$present WHERE id=$record_id";
		if (!($result = $mysqli->query($query))) {
			echo $mysqli->error;
		}

