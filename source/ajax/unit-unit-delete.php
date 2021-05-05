<?php

    session_start();
    include_once('../functions.php');
    require_user_level('lector');
    include_once('../db.php'); /* @var mysqli $mysqli */

	$parent_id = post_escaped("parentID");
	$child_id = post_escaped("childID");

	// fetch type of child unit
    $type = '';
    $query = "SELECT type FROM unit WHERE id='$child_id'";
    if ($result = $mysqli->query($query)) {
        if ($row = $result->fetch_assoc()) {
            // ok
            $type = $row['type'];
        } else {
            // error - no such event
            echo "Unit does not exist.";
            exit();
        }
    } else {
        // error - db error
        echo $mysqli->error;
        exit();
    }

    $mysqli->begin_transaction();
    try {
        // delete association with parent unit
        $query = "DELETE FROM unit_unit WHERE unit_unit.parent_id=$parent_id AND unit_unit.child_id=$child_id";
        if (!$mysqli->query($query)) {
            throw new mysqli_sql_exception("ERROR: Unable to delete association with parent unit.");
        }

        // if type == ocurrence, delete this unit
        if ($type == 'occurrence') {
            $query = "DELETE FROM unit WHERE unit.id=$child_id";
            if (!$mysqli->query($query)) {
                throw new mysqli_sql_exception("ERROR: Unable to delete child unit.");
            }
        }

        $mysqli->commit();
    } catch (mysqli_sql_exception $exception) {
        $mysqli->rollback();
        echo "ERROR\n" . $exception->getMessage();
    }

