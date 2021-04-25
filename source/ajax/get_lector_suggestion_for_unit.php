<?php

	session_start();
	include('../db.php');
	include('../functions.php');

	$unit_id = $_REQUEST["unitID"];

	$query = "SELECT account.name, account.surname, account.id FROM account WHERE is_tutor = TRUE AND id <> 1 AND NOT EXISTS (SELECT 1 FROM unit_account WHERE unit_id =$unit_id AND unit_account.account_id = account.id)";
	$result = $mysqli->query($query);
	if (!is_null($result) && $result->num_rows > 0) {
		$dom = new DOMDocument();
		$dom->encoding = 'utf-8';
		$dom->xmlVersion = '1.0';
		$root = $dom->createElement('DBtransaction');
		while ($row = $result->fetch_assoc()) {	
			$child_node = $dom->createElement('lector', $row['name'] . ' ' . $row['surname']);
			$root->appendChild($child_node);
			$child_node2 = $dom->createElement('id', $row['id']);
			$root->appendChild($child_node2);
		}
		$dom->appendChild($root);
		echo $dom->saveXml();
	}
?>