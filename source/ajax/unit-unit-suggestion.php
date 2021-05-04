<?php

	session_start();
	include('../db.php'); /* @var mysqli $mysqli */
	include('../functions.php');

	$unit_id = $_REQUEST["unitID"];

	$query = "SELECT unit.id, unit.name, unit.type FROM unit"
            . " WHERE NOT EXISTS (SELECT 1 FROM unit_unit WHERE unit_unit.parent_id=$unit_id AND unit_unit.child_id = unit.id)"
	        . " AND (unit.type='event' OR unit.type='singleevent')";
	//$query = "SELECT * FROM unit";
	$result = $mysqli->query($query);
	if (!is_null($result) && $result->num_rows > 0) {
		$dom = new DOMDocument();
		$dom->encoding = 'utf-8';
		$dom->xmlVersion = '1.0';
		$root = $dom->createElement('DBtransaction');
		while ($row = $result->fetch_assoc()) {	
			$child_node = $dom->createElement('unit', $row['name'] . ' (' . $row['type'] . ')');
			$root->appendChild($child_node);
			$child_node2 = $dom->createElement('id', $row['id']);
			$root->appendChild($child_node2);
		}
		$dom->appendChild($root);
		echo $dom->saveXml();
	}
