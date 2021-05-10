<?php

    session_start();
    include('../functions.php');
    require_user_level('lector');
    include('../db.php'); /* @var mysqli $mysqli */

    // get parameters
	$unit_id = post_escaped("unitID");
	$lector_id = post_escaped("lectorID");

    if (!require_user_editor($mysqli, $unit_id, false)) {
        echo "Nemate opravnenie.";
        die();
    }
	
	// query
	$query = "DELETE FROM unit_account WHERE unit_id=$unit_id AND account_id = $lector_id";
		if (!($result = $mysqli->query($query))) {
			echo 'ERROR';
		} else {
			echo 'true';
		}
