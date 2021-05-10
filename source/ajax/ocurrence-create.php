<?php

	session_start();
	include('../db.php');   /* @var mysqli $mysqli */
	include('../functions.php');

	// get GET parameters
	$unit_id = $_REQUEST["unitID"];
	$name = $_REQUEST["name"];

    require_user_editor($mysqli, $unit_id);

	try {
        $mysqli->begin_transaction();

        $parent_event = '';

        // check: is given unitID valid event id?
        $query = "SELECT * FROM unit LEFT JOIN unit_account ON (unit.id = unit_account.unit_id)"
            . " WHERE unit.id='{$unit_id}' AND unit.type='event'";
        if (($result = $mysqli->query($query))) {
            if ($result->num_rows <= 0) {
                echo 'Pre zadanú udalosť nemožno vytvoriť ´vyskyt. (NOT EXIST / NOT EVENT)';
                die();
            }
            $parent_event = $result->fetch_assoc();
        } else {
            echo 'Pre zadanú udalosť nemožno vytvoriť ´vyskyt. (DB ERROR)';
            die();
        }

        // CREATE OCURRENCE
        $query = "INSERT INTO unit SET name='{$name}', author_id='{$_SESSION['user_id']}', type='occurrence', price='{$parent_event['price']}' "
                . ", venue='{$parent_event['venue']}', create_date=NOW(), attendance='{$parent_event['attendance']}', max_clients='{$parent_event['max_clients']}', registration='{$parent_event['registration']}'";
        if (!($result = $mysqli->query($query))) {
            echo 'Pre zadanú udalosť nemožno vytvoriť ´vyskyt. (DB ERROR)';
            die();
        }
        $new_record_id = $mysqli->insert_id;

        // add all event members to ocurrence
        $clients_to_add = array();

        // get all clients in event
        $query = "SELECT client.id FROM client "
            . " WHERE EXISTS (SELECT 1 FROM unit_client WHERE unit_id=$unit_id AND client_id=client.id AND (date_leave IS NULL OR date_leave > NOW()) "
                . "AND (status='manual' OR status='approve' OR status='accept'))";
        $result = $mysqli->query($query);
        if (!is_null($result)) {
            while ($row = $result->fetch_assoc()) {
                array_push($clients_to_add, $row['id']);
            }
        } else {
            throw new mysqli_sql_exception("Error loading list of event members.");
        }

        // add / invite clients
        $author_id = $_SESSION['user_id'];
        foreach ($clients_to_add as $client_id) {
            $query = "INSERT INTO unit_client SET unit_id='$new_record_id', client_id='$client_id', status='manual', author_id='$author_id'";
            if (!($result = $mysqli->query($query))) {
                echo $mysqli->error;
                throw new mysqli_sql_exception("ERROR WHILE ADDIDNG CLIENT ID: " . $client_id);
            }
        }

        // bind ocurrence and parent
        $query = "INSERT INTO unit_unit SET parent_id='{$unit_id}', author_id='{$_SESSION['user_id']}', child_id='{$new_record_id}'";
        if (!($result = $mysqli->query($query))) {
            echo 'Pre zadanú udalosť nemožno vytvoriť ´vyskyt. (DB ERROR)';
            die();
        }

        $mysqli->commit();
    } catch (mysqli_sql_exception $exception) {
	    $mysqli->rollback();
	    echo $mysqli->error;
    }
