<?php

    session_start();
    include('../functions.php');
    require_user_level('lector');
    include('../db.php'); /* @var mysqli $mysqli */

    // get parameters
	$unit_id = post_escaped("unitID");
	$lector_id = post_escaped("lectorID");
	
	// query
	$query = "DELETE FROM unit_account WHERE unit_id=$unit_id AND account_id = $lector_id";
		if (!($result = $mysqli->query($query))) {
			echo 'nevyslo to';
		} else {
			echo 'true';
		}
