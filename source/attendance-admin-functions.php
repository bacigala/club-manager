<?php

// output <tr> for unit-attendance
function unit_get_attendance($mysqli) {
    // unit_id should be set in GET, if not -> error
    $unit_id = post_escaped('unit_id');
    if ($unit_id == '' || !is_integer($unit_id = intval($unit_id))) {
        echo "no attendance for such unit";
        die();
    }

	$query = "SELECT unit_client.id, client.name, client.surname, unit_client.client_id, unit_client.present"
            . " FROM unit_client JOIN client ON (unit_client.client_id = client.id) "
            . " WHERE unit_id ='$unit_id' "
                . " AND (status = 'accept' OR status = 'approce' OR status = 'manual') "
            . " ORDER BY surname ASC";

	$result = db_query($mysqli, $query);		
	if (!is_null($result) && $result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {			
			$output  = '<tr>';
			$output .= '<td>' . $row['name'] . '</td>';
			$output .= '<td>' . $row['surname'] . '</td>';
			$output .= '<td><form method="post" class="table-form" action="client-modify.php">';
			$output .= '<input type="hidden" name="client_id" value="' . $row['client_id'] . '" />';
			$output .= '<input type="checkbox" onclick="toogle_present(this)" id="' . $row['id'] . '" name="vehicle1" value="Bike"' . ($row['present'] == '1' ? ' checked ' : '') . '>';
			$output .= '<label for="' . $row['id'] . '"> Present</label><br>';
			$output .= '</form></td>';

			$output	.= '</tr>';
			echo $output;
		}
		$result->free();
	} else {
	    echo "Bez používatelov.";
    }
}

// create content on client-modify page
function handle_client_modify($mysqli) {
    $request_type = '';
    if (isset($_POST['client_create_request'])) $request_type = 'create_request'; // request from client-overview
    if (isset($_POST['client_modify_request'])) $request_type = 'modify_request';
    if (isset($_POST['client_delete_request'])) $request_type = 'delete_request';
    if (isset($_POST['client_create'])) $request_type = 'create'; // form submit
    if (isset($_POST['client_modify'])) $request_type = 'modify';
    if (isset($_POST['client_delete'])) $request_type = 'delete';

    if (isset($_POST['client_id'])) $item_id = intval(post_escaped('client_id'));

    if ($request_type) {
        try {
            switch ($request_type) {
                case 'create_request':
                    // load fresh form for new client input
                    $data = get_item_form_data();
                    if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
                        $_SESSION['result_message_type'] = 'error';
                        $_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať.';
                        break;
                    }

                    $mysqli->begin_transaction();
                    $query = "INSERT INTO item SET author_id=" . $_SESSION['user_id'];
                    foreach ($data AS $key => $value) $query .= ", $key='$value'";
                    if (!$mysqli->query($query)) {
                        $mysqli->rollback();
                        $_SESSION['result_message_type'] = 'error';
                        $_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať.';
                        break;
                    }

                    $mysqli->commit();
                    $_SESSION['result_message_type'] = 'success';
                    $_SESSION['result_message'] = 'Položka bola vytvorená.';
                    break;
                case 'cancel':
                    $_SESSION['result_message_type'] = 'warning';
                    $_SESSION['result_message'] = 'Položka nebola upravená.';
                    break;
                case 'modify_request':
                    // load user data for modification
                    $data = get_item_form_data();
                    if ((isset($_SESSION['error']) && !empty($_SESSION['error'])) || !$item_id) {
                        $_SESSION['result_message_type'] = 'error';
                        $_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať.';
                        break;
                    }

                    $mysqli->begin_transaction();
                    $query = "UPDATE item SET author_id=" . $_SESSION['user_id'];
                    foreach ($data AS $key => $value) $query .= ", $key='$value'";
                    $query .= ' WHERE id=' . $item_id ;
                    if (!$mysqli->query($query)) {
                        $mysqli->rollback();
                        $_SESSION['result_message_type'] = 'error';
                        $_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať.';
                        break;
                    }

                    $mysqli->commit();
                    $_SESSION['result_message_type'] = 'success';
                    $_SESSION['result_message'] = 'Položka bola upravená.';
                    break;
                case 'delete':
                    // delete item
                    $mysqli->begin_transaction();
                    $query = "DELETE FROM item WHERE id=" . $item_id ;
                    if (!$mysqli->query($query)) {
                        $mysqli->rollback();
                        $_SESSION['result_message_type'] = 'error';
                        $_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať.';
                        break;
                    }

                    $mysqli->commit();
                    $_SESSION['result_message_type'] = 'success';
                    $_SESSION['result_message'] = 'Položka bola odstránená.';
                    break;
            }
        } catch (mysqli_sql_exception $exception) {
            // error
            $mysqli->rollback();
            // set result message
            $_SESSION['result_message_type'] = 'error';
            $_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať. (exception)' . $exception;
        } finally {
            $_SESSION['result_message'] .= $mysqli->error;
            header("Location: item-modify.php");
            exit();
        }
    }


    if (isset($_SESSION['result_message']) && $_SESSION['result_message'] != '') {
        $message = $_SESSION['result_message'];
        $message_type = isset($_SESSION['result_message_type']) ? $_SESSION['result_message_type'] : 'info';

        unset($_SESSION['result_message']);
        unset($_SESSION['result_message_type']);

        echo '<p class="' . $message_type . '">' . $message . '</p>' ;
    }

    if (isset($_SESSION['error'])) {
        foreach ($_SESSION['error'] AS $value) {
            echo '<p class="error">' . $value . '</p>' ;
        }
        unset($_SESSION['error']);
    }

    get_item_form($mysqli);
}