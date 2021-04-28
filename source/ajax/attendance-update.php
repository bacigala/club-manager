<?php

    // VERIFY USER
	session_start();
	include('../functions.php');
    require_user_level('lector');

	// HANDLE REQUEST
    include('../db.php'); /* @var mysqli $mysqli */

	// get parameters
	$record_id = post_escaped('id');
	$present = (post_escaped('value') == 'true' ? '1' : '0');

    // DB query
    $stmt = $mysqli->prepare("UPDATE unit_client SET present=? WHERE id=?");
    $stmt->bind_param("ii", $present, $record_id);
    if (!$stmt->execute()) {
        echo $mysqli->error;
    }
