<?php

    session_start();
    include('../functions.php');
    require_user_level('lector');
    include('../db.php'); /* @var mysqli $mysqli */

	// parameters
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

        if ($status == 'approve' || $status == 'manual' || $status == 'accept') {
            // register payment

            // get items
            $item_ids = array();
            $item_price = array();
            $query = "SELECT item.id, price FROM item WHERE unit_id='$unit_id' AND end_date >= NOW()";
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
                $query = "INSERT INTO payment SET client_id='$entity_id', author_id='$author_id', item_id='{$item_ids[$i]}', create_datetime=NOW(), amount='1', unit_price='{$item_price[$i]}'";
                if (!($result = $mysqli->query($query))) {
                    echo $mysqli->error;
                    throw new mysqli_sql_exception("ERROR WHILE ADDIDNG CLIENT ID: " . $entity_id);
                }
            }
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


            if ($status == 'approve' || $status == 'manual' || $status == 'accept') {
                // register payment

                // get items
                $item_ids = array();
                $item_price = array();
                $query = "SELECT item.id, price FROM item WHERE unit_id='$unit_id' AND end_date >= NOW()";
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

                foreach ($clients_to_add as $client_id) {
                    for ($i = 0; $i < count($item_ids); $i++) {
                        $query = "INSERT INTO payment SET client_id='$client_id', author_id='$author_id', item_id='{$item_ids[$i]}', create_datetime=NOW(), amount='1', unit_price='{$item_price[$i]}'";
                        if (!($result = $mysqli->query($query))) {
                            echo $mysqli->error;
                            throw new mysqli_sql_exception("ERROR WHILE ADDIDNG CLIENT ID: " . $client_id);
                        }
                    }
                }
            }

            $mysqli->commit();
        } catch (mysqli_sql_exception $e) {
            $mysqli->rollback();
            echo "umexpected error, course was not added / invited to unit" . $e;
        }
    }
