<?php

/**
 * Output <tr> of lectors units of some type
 * @param $mysqli mysqli
 * @param $type string course/event/occurence
 */
function get_units_of_lector($mysqli, $type) {
	$query = "SELECT unit.id, unit.name, unit.type, unit.registration, unit.author_id, unit_clients.no_clients, unit.attendance, unit.max_clients, unit_account.is_editor, no_clients, unit_account.is_editor"
			. " FROM unit LEFT JOIN unit_clients ON (unit.id = unit_clients.unit_id) "
			. " LEFT JOIN unit_account ON (unit.id = unit_account.unit_id)"
			. " WHERE "
			. " (unit.author_id = " . $_SESSION['user_id'] . " OR (account_id = "  . $_SESSION['user_id'] . '))';
	if ($type == 'course') 		$query .= " AND type = 'course'";
	if ($type == 'event')  		$query .= " AND (type = 'event' OR type = 'singleevent')";
	if ($type == 'occurence') $query .= " AND type = 'occurrence'";
	
	$query .= " GROUP BY unit.id ORDER BY unit.name ASC ";	

	$result = db_query($mysqli, $query);		
	if (!is_null($result) && $result->num_rows > 0) {

	    // fetch highlight request from $_GET
        $highlight_id = 0;
        if (isset($_GET['unitId'])) $highlight_id = intval($_GET['unitId']);

		while ($row = $result->fetch_assoc()) {
		    $onclick = ' onclick="load_unit_details(this, ' . $row['id'] . ')" ';
		    $class = ($row['id'] == $highlight_id ? ' class="warn " ' : '');

			$output  = '<tr ' . $onclick . $class . '>'; // onClick -> ajax load of unit details
			$output .= '<td>' . $row['name'] . '</td>';
			
			if ($type == 'event')	{
				if ($row['type'] == 'event') $output .= '<td>Udalosť s výskytmi</td>';
				if ($row['type'] == 'singleevent') $output .= '<td>Jednorazová udalosť</td>';
			}
			
			$output .= '<td>' . (is_null($row['no_clients']) ? '0' : $row['no_clients']) . '/' . $row['max_clients'] . '</td>';
			
			switch ($row['registration']) {
				case 'open':
					$output .= '<td>Otvorená</td>';
					break;					
				case 'close':
					$output .= '<td>Uzavretá</td>';
					break;
				case 'invite':
					$output .= '<td>Na pozvánku</td>';
					break;
				case 'request':
					$output .= '<td>Na požiadanie</td>';
					break;
			}
			
			$output .= '<td>';
			$output .= '<form method="post" class="table-form" action="unit-admin-overview.php">';
			$output .= '	<input type="hidden" name="unit_id" value="' . $row['id'] . '" />';
			$unit_id = $row['id'];
			if ($row['author_id'] == $_SESSION['user_id']) $output .= "<input type='button' value='todo Odstrániť' onclick='unit_delete($unit_id)'/>";
			$output .= '</form>';

			if ($row['type'] == 'singleevent' && $row['attendance'] == '1') {
                $output .= '<form method="post" class="table-form" action="attendance-admin-overview.php">';
                $output .= '<input type="hidden" name="unit_id" value="' . $row['id'] . '" />';
                $output .= '<input type="submit" name="" value="Dochádzka" />';
                $output .= '</form>';
            }


			$output .= '</td>';
			$output	.= '</tr>';
			
			$colspan = ($type == 'event') ? 5 : 4;
			$output .= '<tr style="display: none;"><td colspan="' . $colspan . '"><div class="unit_detail"></div></td></tr>'; // div to render unit_details
			
			echo $output;
		}
		$result->free();
	}
}
