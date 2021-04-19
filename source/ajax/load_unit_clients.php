<?php

	session_start();
	include('../db.php');
	include('../functions.php');

	// get GET parameters
	$unit_id = $_REQUEST["unitID"];
	
	$query = "SELECT * FROM unit_client JOIN client  ON (unit_client.client_id = client.id) WHERE unit_client.unit_id = $unit_id AND date_leave IS NULL ";
	$query .= " AND status<>'retract' AND status<>'restrict' ORDER BY status ASC";
	$result = db_query($mysqli, $query);		
	if (!is_null($result) && $result->num_rows > 0) {
		$output = '<table>';
		while ($row = $result->fetch_assoc()) {	
			$output .= '<tr>';
			$output .= ' <td>' . $row['name'] . ' ' . $row['surname'] . ' (od ' . date_format(date_create($row['date_join']), "d-m-Y") . ')</td>';
			$output .= ' <td>' . $row['status'] . '</td>';
			$output .= ' <td><form>';
			
			// show oprions according to status
			if ($row['status'] == 'manual' || $row['status'] == 'accept' || $row['status'] == 'approve') {
				$output .= '  <input type="button" name="" value="Vyhodit" onClick="update_unit_client_status(this, \'' . $row['unit_id'] . '\', \'' . $row['client_id'] . '\', \'leave\');">';
			}
			if ($row['status'] == 'request') {
				$output .= '  <input type="button" name="" value="Povolit" onClick="update_unit_client_status(this, \'' . $row['unit_id'] . '\', \'' . $row['client_id'] . '\', \'approve\');">';
				$output .= '  <input type="button" name="" value="Zamietnut" onClick="update_unit_client_status(this, \'' . $row['unit_id'] . '\', \'' . $row['client_id'] . '\', \'restrict\');">';
			}
			if ($row['status'] == 'invite') {
				$output .= '  <input type="button" name="" value="Stiahnut pozvanku" onClick="update_unit_client_status(this, \'' . $row['unit_id'] . '\', \'' . $row['client_id'] . '\', \'leave\');">';
			}
			
			$output .= ' </form></td>';
			$output .= '</tr>';
		
		
			// $record = $dom->createElement('record');
				// $name = $dom->createElement('name', $row['name'] . $row['surname']);
					// $record->appendChild($name);
				// $editor = $dom->createElement('editor', $row['is_editor']);
					// $record->appendChild($editor);
				// $root->appendChild($record);
		}
		// optiopn to create new client (invitation / manual)
		
		
		$output .= '</table>';
		echo $output;
	} else {
		//error
		echo 'no clients';
		// $child_node_result_status = $dom->createElement('status', 'ERR');
		// $root->appendChild($child_node_result_status);
		// $child_node_result_message = $dom->createElement('message', 'ERROR');
		// $root->appendChild($child_node_result_message);
		// $dom->appendChild($root);
	}
	
	//echo $dom->saveXml();
?>