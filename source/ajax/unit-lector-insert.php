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
	$lector_id = $_REQUEST["lectorID"];

	// query DB
	$query = "INSERT INTO unit_account SET unit_id=$unit_id, account_id = $lector_id";
	echo $query;
    if ($mysqli->query($query)) {
        echo '';
    } else {
        echo $mysqli->error;
    }
