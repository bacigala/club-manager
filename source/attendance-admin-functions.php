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

function get_unit_detail($mysqli) {
    $unit_id = intval(post_escaped('unit_id'));
    if ($unit_id < 1) {
        echo "no attendance for such unit";
        die();
    }

    $query = "SELECT * FROM unit WHERE id ='$unit_id'";

    $result = db_query($mysqli, $query);
    if (!is_null($result) && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $output  = '<p class="info">';
        $output .= "Udalosť: {$row['name']} čas: {$row['start_datetime']} - {$row['end_datetime']}";
        $output .= '</p>';
        echo $output;
    } else {
        echo "Bez informacii.";
    }
}
