<?php

    session_start();
    include('../functions.php');
    require_user_level('admin');
    include('../db.php'); /* @var mysqli $mysqli */

    // parameters
	$parent_id = $_REQUEST["parentID"];
	$child_id = $_REQUEST["childID"];

    require_user_editor($mysqli, $parent_id);

	// query DB
	$query = "INSERT INTO unit_unit SET parent_id=$parent_id, child_id = $child_id, author_id={$_SESSION['user_id']}";
    if (!$mysqli->query($query)) {
        echo $mysqli->error;
    }
