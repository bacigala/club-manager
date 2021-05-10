<?php

    session_start();
    include('../functions.php');
    require_user_level('lector');
    include('../db.php'); /* @var mysqli $mysqli */

	// get GET parameters
	$unit_id = $_REQUEST["unitID"];
	$client_id = $_REQUEST["clientID"];
	$status = $_REQUEST["status"];

    require_user_editor($mysqli, $unit_id);

try {
    $mysqli->begin_transaction();

    // update unit_client
    $query = '';
    if ($status == 'leave') {
        $query = "UPDATE unit_client SET date_leave=NOW() WHERE unit_id=$unit_id AND client_id = $client_id";
    } else {
        $query = "UPDATE unit_client SET status = '$status' WHERE unit_id=$unit_id AND client_id = $client_id";
    }

    if (!($result = $mysqli->query($query))) {
        echo 'ERROR';
        throw new mysqli_sql_exception("unable to update unit_client");
    }

    if ($status == 'approve' || $status == 'manual' || $status == 'accept') {
        // register payment

        // get items
        $item_ids = array();
        $item_price = array();
        $query = "SELECT item.id, price FROM item "
            . " WHERE unit_id='$unit_id' AND end_date >= NOW()";
        $result = $mysqli->query($query);
        if (!is_null($result)) {
            while ($row = $result->fetch_assoc()) {
                array_push($item_ids, $row['id']);
                array_push($item_price, $row['price']);
            }
        } else {
            throw new mysqli_sql_exception("Error loading list of event items.");
        }

        // create payment obligation
        $author_id = '1';
        for ($i = 0; $i < count($item_ids); $i++) {
            $query = "INSERT INTO payment SET client_id='$client_id', author_id='$author_id', item_id='{$item_ids[$i]}', create_datetime=NOW(), amount='1', unit_price='{$item_price[$i]}'";
            if (!($result = $mysqli->query($query))) {
                echo $mysqli->error;
                throw new mysqli_sql_exception("ERROR WHILE ADDIDNG CLIENT ID: " . $client_id);
            }
        }
    }

    $mysqli->commit();
} catch (mysqli_sql_exception $exception) {
    $mysqli->rollback();
    echo $mysqli->error;
}