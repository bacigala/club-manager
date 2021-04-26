<?php

    // user needs to be tutor (lector)
    session_start();
    if (!isset($_SESSION['user_is_tutor']) || !$_SESSION['user_is_tutor']) {
        echo 'Na prístup k tejto stránke nemáte oprávnenie.';
        die();
    }

    include('../db.php');
    include('../functions.php');

    // get GET parameters
    $unit_id = $_REQUEST["unitID"];

    // query DB
	$query = "SELECT * FROM unit_client JOIN client ON (unit_client.client_id = client.id) WHERE unit_client.unit_id = $unit_id AND date_leave IS NULL ";
	$query .= " AND status<>'retract' AND status<>'restrict' ORDER BY status ASC";
	$result = db_query($mysqli, $query);
    $output = '<table>';
	if (!is_null($result) && $result->num_rows > 0) {
        // query ok -> populate table
		while ($row = $result->fetch_assoc()) {	
			$output .= '<tr>';
			$output .= ' <td>' . $row['name'] . ' ' . $row['surname'] . ' (od ' . date_format(date_create($row['date_join']), "d-m-Y") . ')</td>';
			$output .= ' <td>' . $row['status'] . '</td>';
			$output .= ' <td><form>';
			
			// show options according to status
            switch ($row['status']) {
                case 'manual':
                case 'accept':
                case 'approve':
                    $output .= '<input type="button" name="" value="Vyhodit" onClick="update_unit_client_status(this, \'' . $row['unit_id'] . '\', \'' . $row['client_id'] . '\', \'leave\');">';
                    break;
                case 'request':
    				$output .= '<input type="button" name="" value="Povolit" onClick="update_unit_client_status(this, \'' . $row['unit_id'] . '\', \'' . $row['client_id'] . '\', \'approve\');">';
	    			$output .= '<input type="button" name="" value="Zamietnut" onClick="update_unit_client_status(this, \'' . $row['unit_id'] . '\', \'' . $row['client_id'] . '\', \'restrict\');">';
		            break;
                case 'invite':
                    $output .= '<input type="button" name="" value="Stiahnut pozvanku" onClick="update_unit_client_status(this, \'' . $row['unit_id'] . '\', \'' . $row['client_id'] . '\', \'leave\');">';
                    break;
			}
			
			$output .= ' </form></td>';
			$output .= '</tr>';
		}
	} else {
		// error || no clients found
        $output .= '<tr>';
        $output .= ' <td colspan="2">No clients found</td>';
        $output .= '</tr>';
	}

    // option to add/invite client
    $output .= '<tr>';
    $output .= ' <td colspan="2">';
    $output .=   '<form autocomplete="off">';
    $output .=    '<div class="autocomplete">';
    $output .=     '<input class="clientSearch' . $unit_id . '" type="text" name="name" placeholder="Ferko">';
    $output .=    '</div>';
    $output .=   '</form>';
    $output .=  '</td>';
    $output .= '</tr>';

    $output .= '</table>';
    echo $output;
