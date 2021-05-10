<?php

    session_start();
    include('../functions.php');
    require_user_level('lector');
    include('../db.php'); /* @var mysqli $mysqli */

	$id = $_REQUEST["unitID"];
	$property = $_REQUEST["property"];
	$value = $_REQUEST["value"];

	$status_ok = false;

    if (!require_user_editor($mysqli, $id, false)) {
        echo "Nemate opravnenie.";
        die();
    }

    $query = "UPDATE unit SET $property = '$value' WHERE id=$id";
    $status_ok = ($result = $mysqli->query($query));

    if ($property == 'start_datetime' || $property == 'end_datetime') {
        $value = input_date_format($value);
    }

    $dom = new DOMDocument();
    $dom->encoding = 'utf-8';
    $dom->xmlVersion = '1.0';
    $root = $dom->createElement('DBtransaction');
    $child_node_result_status = $dom->createElement('status', $status_ok ? 'OK' : "FAIL");
    $root->appendChild($child_node_result_status);
    $child_node_result_message = $dom->createElement('message', '');
    $root->appendChild($child_node_result_message);
    $child_node_new_value = $dom->createElement('value', $value);
    $root->appendChild($child_node_new_value);
    $dom->appendChild($root);
	
	echo $dom->saveXml();
