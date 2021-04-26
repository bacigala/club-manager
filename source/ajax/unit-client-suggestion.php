<?php

    // user needs to be tutor (lector)
    session_start();
    if (!isset($_SESSION['user_is_tutor']) || !$_SESSION['user_is_tutor']) {
        echo 'Na prístup k tejto stránke nemáte oprávnenie.';
        die();
    }

	include('../db.php');
	include('../functions.php');

	// get request parameters
	$unit_id = $_REQUEST["unitID"];

	// query
	$query = "SELECT client.name, client.surname, client.id FROM client "
	            . " WHERE NOT EXISTS (SELECT 1 FROM unit_client WHERE unit_id=$unit_id AND client_id=client.id AND (date_leave IS NULL OR date_leave > NOW()))";
	$result = $mysqli->query($query);
	if (!is_null($result) && $result->num_rows > 0) {
		$dom = new DOMDocument();
		$dom->encoding = 'utf-8';
		$dom->xmlVersion = '1.0';
		$root = $dom->createElement('DBtransaction');
		while ($row = $result->fetch_assoc()) {	
			$child_node = $dom->createElement('client', $row['name'] . ' ' . $row['surname']);
			$root->appendChild($child_node);
			$child_node2 = $dom->createElement('id', $row['id']);
			$root->appendChild($child_node2);
		}
		$dom->appendChild($root);
		echo $dom->saveXml();
	} else {
	    echo $mysqli->error;
    }
