<?php

	session_start();
	include('../db.php'); /* @var mysqli $mysqli */
	include('../functions.php');

	$unit_id = $_REQUEST["unitID"];
	$suggestion_type = $_REQUEST["type"];

	switch ($suggestion_type) {
        case 'lector':
            $query = "SELECT account.name, account.surname, account.id FROM account WHERE is_tutor = TRUE AND id <> 1 AND NOT EXISTS (SELECT 1 FROM unit_account WHERE unit_id =$unit_id AND unit_account.account_id = account.id)";
            $result = $mysqli->query($query);
            if (!is_null($result) && $result->num_rows > 0) {
                $dom = new DOMDocument();
                $dom->encoding = 'utf-8';
                $dom->xmlVersion = '1.0';
                $root = $dom->createElement('DBtransaction');
                while ($row = $result->fetch_assoc()) {
                    $child_node = $dom->createElement('suggestion', $row['name'] . ' ' . $row['surname']);
                    $root->appendChild($child_node);
                    $child_node2 = $dom->createElement('id', $row['id']);
                    $root->appendChild($child_node2);
                }
                $dom->appendChild($root);
                echo $dom->saveXml();
            }
            break;
        case 'client':
            $dom = new DOMDocument();
            $dom->encoding = 'utf-8';
            $dom->xmlVersion = '1.0';
            $root = $dom->createElement('DBtransaction');

            // suggest clients not yet added / invited to the unit
            $query = "SELECT client.name, client.surname, client.id FROM client "
                . " WHERE NOT EXISTS (SELECT 1 FROM unit_client WHERE unit_id=$unit_id AND client_id=client.id AND (date_leave IS NULL OR date_leave > NOW()))";
            $result = $mysqli->query($query);
            if (!is_null($result) && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $child_node = $dom->createElement('suggestion', $row['name'] . ' ' . $row['surname']);
                    $root->appendChild($child_node);
                    $root->appendChild($dom->createElement('type', 'client'));
                    $root->appendChild($dom->createElement('id', $row['id']));
                }
            } else {
                echo $mysqli->error;
            }

            // suggest courses (groups of clients)
            $query = "SELECT unit.name, unit.id FROM unit";
            $result = $mysqli->query($query);
            if (!is_null($result) && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $child_node = $dom->createElement('suggestion', $row['name']);
                    $root->appendChild($child_node);
                    $root->appendChild($dom->createElement('type', 'course'));
                    $root->appendChild($dom->createElement('id', $row['id']));
                }
            } else {
                echo $mysqli->error;
            }

            $dom->appendChild($root);
            echo $dom->saveXml();
            break;
        case 'unit':
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
                    $child_node = $dom->createElement('suggestion', $row['name'] . ' (' . $row['type'] . ')');
                    $root->appendChild($child_node);
                    $child_node2 = $dom->createElement('id', $row['id']);
                    $root->appendChild($child_node2);
                }
                $dom->appendChild($root);
                echo $dom->saveXml();
            }
            break;





    }
