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
	$query = "SELECT * FROM unit_account JOIN account  ON (unit_account.account_id  = account.id) WHERE unit_account.unit_id = $unit_id";
	$result = db_query($mysqli, $query);		
	$output = '<table>';
	if (!is_null($result) && $result->num_rows > 0) {
	    // query ok -> populate table
		while ($row = $result->fetch_assoc()) {	
			$output .= '<tr>';
			$output .= '<td>' . $row['name'] . ' ' . $row['surname'] . '</td>';
			$output .= '<td><form>';
            $output .= '<label for="is_editor'. $row['unit_id'] . 'a' . $row['account_id']  . '">Editor</label>';
			$output .= '  <input type="checkbox" id="is_editor'. $row['unit_id'] . 'a' . $row['account_id']  . '" name="is_editor'. $row['unit_id'] . 'a' . $row['account_id']  . '" value="true" ' . ($row['is_editor'] ? 'checked' : '') . '>';
			$output .= '  <input type="button" class="main-form-option-button" name="" value="Uložiť" onClick="update_unit_lector(this, \'' . $row['unit_id'] . '\', \'' . $row['account_id'] . '\');">';
			$output .= '  <input type="button" class="main-form-option-button" name="" value="Odstraniť" onClick="delete_unit_lector(this, \'' . $row['unit_id'] . '\', \'' . $row['account_id'] . '\');">';
			$output .= ' </form></td>';
			$output .= '</tr>';
		}
	} else {
	    // no lectors found
		$output .= '<tr>';
		$output .= ' <td colspan="2">No lectors found</td>';
		$output .= '</tr>';
	}
	
    // option to add lector - autocomplete
    $output .= '<tr>';
    $output .= ' <td colspan="2">';
    $output .=   '<form autocomplete="off" class="autocomplete-form">';
    $output .=    '<div class="autocomplete">';
    $output .=     '<input class="lectorSearch' . $unit_id . '" type="text" name="name" placeholder="Ferko">';
    $output .=    '</div>';
    $output .=   '</form>';
    $output .=  '</td>';
    $output .= '</tr>';
	
	$output .= '</table>';
	echo $output;
