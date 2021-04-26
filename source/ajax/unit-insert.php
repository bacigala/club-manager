<?php

    // user needs to be tutor
    session_start();
    if (!isset($_SESSION['user_is_tutor']) || !$_SESSION['user_is_tutor']) {
        echo 'Na prístup k tejto stránke nemáte oprávnenie.';
        die();
    }

	include('../db.php');

	// get request parameters
	$unit_name = $_REQUEST["unitName"];
	$unit_type = $_REQUEST["unitType"];

	// query DB
    $author_id = $_SESSION['user_id'];
	$query = "INSERT INTO unit SET name='$unit_name', type='$unit_type', author_id='$author_id'";

	if (!($result = $mysqli->query($query))) {
		echo $mysqli->error;
	} else {
		echo $mysqli->insert_id;
		$result->free();
	}
