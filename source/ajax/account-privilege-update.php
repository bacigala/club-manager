<?php

	session_start();
	include('../functions.php');
    require_user_level('admin');
    include('../db.php'); /* @var mysqli $mysqli */

    // parameters
	$account_id = post_escaped('id');
	$key = post_escaped('key');
	$value = post_escaped('value') == 'true' ? 1 : 0;

	// check: one cannot update own privileges
    if ($account_id == $_SESSION['user_id']) {
        echo "Úprava vlastných práv nie je povolená.";
        die();
    }

    // query
    $stmt = $mysqli->prepare("UPDATE account SET $key=? WHERE id=?");
    $stmt->bind_param("ii", $value, $account_id);
    if (!$stmt->execute()) {
        echo $mysqli->error;
    }
