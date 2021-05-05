<?php

    // user needs to be tutor
    session_start();
    if (!isset($_SESSION['user_is_tutor']) || !$_SESSION['user_is_tutor']) {
        echo 'Na prístup k tejto stránke nemáte oprávnenie.';
        die();
    }

	include('../db.php');

    // get request parameters
	$parent_id = $_REQUEST["parentID"];
	$child_id = $_REQUEST["childID"];

	// query DB
	$query = "INSERT INTO unit_unit SET parent_id=$parent_id, child_id = $child_id, author_id={$_SESSION['user_id']}";
    if (!$mysqli->query($query)) {
        echo $mysqli->error;
    }
