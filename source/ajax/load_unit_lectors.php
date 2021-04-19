<?php

	session_start();
	include('../db.php');
	include('../functions.php');

	// get GET parameters
	$unit_id = $_REQUEST["unitID"];
	
	// build result XML
	// $dom = new DOMDocument();
	// $dom->encoding = 'utf-8';
	// $dom->xmlVersion = '1.0';
	// $root = $dom->createElement('DBtransaction');
	
	$query = "SELECT * FROM unit_account JOIN account  ON (unit_account.account_id  = account.id) WHERE unit_account.unit_id = $unit_id";
	$result = db_query($mysqli, $query);		
	$output = '<table>';
	if (!is_null($result) && $result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {	
			$output .= '<tr>';
			$output .= ' <td>' . $row['name'] . ' ' . $row['surname'] . '</td>';
			$output .= ' <td><form>';
			$output .= '  Editor <input type="checkbox" id="is_editor" name="is_editor" value="true" ' . ($row['is_editor'] ? 'checked' : '') . '>';
			$output .= '  <input type="button" name="" value="Uložiť" onClick="update_unit_lector(this, \'' . $row['unit_id'] . '\', \'' . $row['account_id'] . '\');">';
			$output .= '  <input type="button" name="" value="Odstraniť" onClick="delete_unit_lector(this, \'' . $row['unit_id'] . '\', \'' . $row['account_id'] . '\');">';
			$output .= ' </form></td>';
			$output .= '</tr>';
		}
	} else {
		// option to add lector
		$output .= '<tr>';
		$output .= ' <td colspan="2">No lectors found</td>';
		$output .= '</tr>';
	}
	
		// option to add lector
		$output .= '<tr>';
		$output .= ' <td colspan="2"><form>';
		$output .= ' <input type="text" id="name" name="name" value="">';
		$output .= '  Editor <input type="checkbox" id="is_editor" name="is_editor" value="true">';
		$output .= '  <input type="button" name="" value="Pridat" onClick="add_unit_lector(this, \'' . $unit_id . '\', \'manual\');">';
		$output .= ' </form></td>';
		$output .= '</tr>';
	
	$output .= '</table>';
	echo $output;
?>