<?php

    /*
     * Create ocurrence for given unit_id
     */

	session_start();
	include('../db.php');   /* @var mysqli $mysqli */
	include('../functions.php');
	//require_user_level('lector');

	// get GET parameters
	$unit_id = $_REQUEST["unitID"];
	$name = $_REQUEST["name"];

	try {
        $mysqli->begin_transaction();

        // check: is given unitID valid event id?
        // check: logged-in user needs to have right to modify event this event
        $query = "SELECT * FROM unit LEFT JOIN unit_account ON (unit.id = unit_account.unit_id)"
            . " WHERE unit.id='{$unit_id}' AND unit.type='event'"
            . " AND (unit.author_id='{$_SESSION['user_id']}' OR (unit_account.account_id='{$_SESSION['user_id']}' AND unit_account.is_editor='1'))";
        if (($result = $mysqli->query($query))) {
            if ($result->num_rows <= 0) {
                echo 'Pre zadanú udalosť nemožno vytvoriť ´vyskyt. (NOT EXIST / NOT EVENT)';
                die();
            }
        } else {
            echo 'Pre zadanú udalosť nemožno vytvoriť ´vyskyt. (DB ERROR)';
            die();
        }

        // CREATE OCURRENCE
        $query = "INSERT INTO unit SET name='{$name}', author_id='{$_SESSION['user_id']}', type='occurrence'";
        if (!($result = $mysqli->query($query))) {
            echo 'Pre zadanú udalosť nemožno vytvoriť ´vyskyt. (DB ERROR)';
            die();
        }
        $new_record_id = $mysqli->insert_id;

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


