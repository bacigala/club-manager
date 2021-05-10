<?php

	session_start();
	include('../functions.php');
    require_user_level('lector');
    include('../db.php'); /* @var mysqli $mysqli */

	// parameters
	$record_id = post_escaped('id');
	$present = (post_escaped('value') == 'true' ? '1' : '0');

	// check: logged=in user needs to be admin / unit author / unit editor
    $check = false;
    $logged_in_user_id = $_SESSION['user_id'];
    $query = "SELECT unit_id FROM unit_client WHERE id='$record_id'";
    $result = db_query($mysqli, $query);
    if (!is_null($result) && $result->num_rows > 0) {
        if ($row = $result->fetch_assoc()) {
            $check = require_user_editor($mysqli, $row['unit_id'], false);
        }
    }
    if (!$check) {
        echo 'ERROR: Insufficient privileges to perform operation.';
        die();
    }

    // query
    $stmt = $mysqli->prepare("UPDATE unit_client SET present=? WHERE id=?");
    $stmt->bind_param("ii", $present, $record_id);
    if (!$stmt->execute()) {
        echo $mysqli->error;
    }
