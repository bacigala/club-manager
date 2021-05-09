<?php

    // user needs to be tutor
    session_start();
    if (!isset($_SESSION['user_is_tutor']) || !$_SESSION['user_is_tutor']) {
        echo 'Na prístup k tejto stránke nemáte oprávnenie.';
        die();
    }

	include('../db.php'); /* @var $mysqli */

	// get request parameters
	$unit_id = $_REQUEST["unitID"];
	$entity_id = $_REQUEST["entityId"];
	$entity_type = $_REQUEST["entityType"];
	$status = $_REQUEST["status"];

	if ($entity_type == 'client') {
        // query DB
        $author_id = $_SESSION['user_id'];
        $query = "INSERT INTO unit_client SET unit_id='$unit_id', client_id='$entity_id', status='$status', author_id='$author_id'";

        if (!($result = $mysqli->query($query))) {
            echo $mysqli->error;
        } else {
            echo '';
        }
    }

	if ($entity_type == 'course') {
	    $clients_to_add = array();

	    // get all clients in course not added to unit yet
        $query = "SELECT client.id FROM client "
            . " WHERE NOT EXISTS (SELECT 1 FROM unit_client WHERE unit_id=$unit_id AND client_id=client.id AND (date_leave IS NULL OR date_leave > NOW()))"
            . " AND EXISTS (SELECT 1 FROM unit_client WHERE unit_id=$entity_id AND client_id=client.id AND (date_leave IS NULL OR date_leave > NOW()))";
        $result = $mysqli->query($query);
        if (!is_null($result) && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                array_push($clients_to_add, $row['id']);
            }
        } else {
            echo $mysqli->error;
            die();
        }

        // add / invite clients
        $author_id = $_SESSION['user_id'];

        try {
            $mysqli->begin_transaction();
            foreach ($clients_to_add as $client_id) {
                $query = "INSERT INTO unit_client SET unit_id='$unit_id', client_id='$client_id', status='$status', author_id='$author_id'";
                if (!($result = $mysqli->query($query))) {
                    echo $mysqli->error;
                    throw new mysqli_sql_exception("ERROR WHILE MANIPULATION CLIENT ID: " . $client_id);
                }
            }
            $mysqli->commit();
        } catch (mysqli_sql_exception $e) {
            $mysqli->rollback();
            echo "umexpected error, course was not added / invited to unit" . $e;
        }
    }
