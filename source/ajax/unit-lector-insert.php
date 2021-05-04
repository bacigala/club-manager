<?php

    session_start();
    include('../functions.php');
    require_user_level('lector');
    include('../db.php'); /* @var mysqli $mysqli */

    // get parameters
    $unit_id = post_escaped("unitID");
    $lector_id = post_escaped("lectorID");

	// query DB
	$query = "INSERT INTO unit_account SET unit_id=$unit_id, account_id = $lector_id";
    if (!$mysqli->query($query))
        echo "Lektora nebolo možné pridať k udalosti.\n" . $mysqli->error;
