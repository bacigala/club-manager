<?php

    // user needs to be tutor
    session_start();
    if (!isset($_SESSION['user_is_tutor']) || !$_SESSION['user_is_tutor']) {
        echo 'Na prístup k tejto stránke nemáte oprávnenie.';
        die();
    }

	include('../db.php');

	// get request parameters
	$unit_id = $_REQUEST["unitID"];
	$client_id = $_REQUEST["clientID"];
	$status = $_REQUEST["status"];

	// query DB
    $author_id = $_SESSION['user_id'];
	$query = "INSERT INTO unit_client SET unit_id='$unit_id', client_id='$client_id', status='$status', author_id='$author_id'";

	if (!($result = $mysqli->query($query))) {
		echo $mysqli->error;
	} else {
		echo '';
	}
