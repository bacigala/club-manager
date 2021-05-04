<?php

    // user needs to be tutor (lector)
    session_start();
    include('../functions.php');
    require_user_level('lector');
	include('../db.php'); /* @var $mysqli */

	// get GET parameters
	$unit_id = $_REQUEST["unitID"];
	//$type = $_REQUEST["type"];

	$query = "SELECT type FROM unit WHERE id=$unit_id";
    $result = db_query($mysqli, $query);
    if (!is_null($result) && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['type'] == 'course') $type = 'event';
        if ($row['type'] == 'event') $type = 'occurence';
    }


	// QUERY
    $query = "SELECT unit.id, unit.name, unit.type, unit.registration, unit.author_id, unit_clients.no_clients, unit.attendance, unit.max_clients, no_clients" //, unit_account.is_editor"
        . " FROM `unit_unit` JOIN unit ON (unit_unit.child_id = unit.id) LEFT JOIN unit_clients ON (unit.id = unit_clients.unit_id) "
        . " WHERE unit_unit.parent_id = $unit_id";

    if ($type == 'course')      $query .= " AND unit.type = 'course'";
    if ($type == 'event')  		$query .= " AND (unit.type = 'event' OR unit.type = 'singleevent')";
    if ($type == 'occurence')   $query .= " AND unit.type = 'occurrence'";


	$result = db_query($mysqli, $query);
	$output = '<table>';
	$has_output = false;
	if (!is_null($result) && $result->num_rows > 0) {
	    $has_output = true;
	    // query ok -> populate table
		while ($row = $result->fetch_assoc()) {
            $onclick = ' onclick="load_unit_details(this, ' . $row['id'] . ')" ';
            $output  .= '<tr ' . $onclick . '>'; // onClick -> ajax load of unit details

            // name
            $output .= "<td class='unit_{$row['id']}_name_label'>{$row['name']}</td>";

            // type
            if ($type == 'event')	{
                if ($row['type'] == 'event') $output .= '<td>Udalosť s výskytmi</td>';
                if ($row['type'] == 'singleevent') $output .= '<td>Jednorazová udalosť</td>';
            }

            // capacity
            $output .= "<td><span class='unit_{$row['id']}_no_clients_label'>" . (is_null($row['no_clients']) ? '0' : $row['no_clients']) . '</span>/'
                . "<span class='unit_{$row['id']}_max_clients_label'>" . $row['max_clients'] . '</span></td>';

            // registration
            $output .= "<td class='unit_{$row['id']}_registration_label'>";
            switch ($row['registration']) {
                case 'open':
                    $output .= 'Otvorená</td>';
                    break;
                case 'close':
                    $output .= 'Uzavretá</td>';
                    break;
                case 'invite':
                    $output .= 'Na pozvánku</td>';
                    break;
                case 'request':
                    $output .= 'Na požiadanie</td>';
                    break;
            }

            $output .= '<td>';
            $output .= '<form method="post" class="table-form" action="unit-admin-overview.php">';
            $output .= '	<input type="hidden" name="unit_id" value="' . $row['id'] . '" />';
            //$unit_id = $row['id']; // todo neprepisuj tu unit_id, neskor je este potrebne
            //if ($row['author_id'] == $_SESSION['user_id']) $output .= "<input type='button' value='todo Odstrániť' onclick='unit_delete($unit_id)' class='main-form-option-button'/>";
            $output .= '</form>';

            if ($row['type'] == 'singleevent' && $row['attendance'] == '1') {
                $output .= '<form method="post" class="table-form" action="attendance-admin-overview.php">';
                $output .= '<input type="hidden" name="unit_id" value="' . $row['id'] . '" />';
                $output .= '<input type="submit" name="" value="Dochádzka" class="main-form-option-button" />';
                $output .= '</form>';
            }

            $output .= '</td>';
            $output	.= '</tr>';

            $colspan = ($type == 'event') ? 5 : 4;
            $output .= '<tr style="display: none;" class="unit_detail_container"><td colspan="' . $colspan . '"><div class="unit_detail"></div></td></tr>'; // div to render unit_details into


		}
	} else {
	    // no events / ocurrences found
		$output .= '<tr>';
		$output .= ' <td colspan="2">No ' . ($type == 'event' ? 'events' : 'ocurrences' ) . ' found</td>';
		$output .= '</tr>';
	}

	// todo OPTION TO ADD OCURRENCE FOR EVENT
    if ($type == 'occurence') {
        $output .= '<tr>';
        $output .= ' <td colspan="' . ($has_output ? '3' : '1') . '">';
        $output .= '<div class="autocomplete-form">';
        $output .= '<div class="autocomplete">';
        $output .= "<button class='button-create-new' onclick='create_ocurrence(this, {$unit_id})'>Nový výskyt</button>";
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</td>';
        $output .= '</tr>';
    }




    // OPTION TO ADD EVENT / SINGLEEVENT TO COURSE
    if ($type == 'event') {
        $output .= '<tr>';
        $output .= ' <td colspan="' . ($has_output ? '5' : '2') . '">';
        $output .= '<form autocomplete="off" class="autocomplete-form">';
        $output .= '<div class="autocomplete">';
        $output .= '<input class="unitSearch' . $unit_id . '" type="text" name="name" placeholder="Udalost vo stvrtok">';
        $output .= '</div>';
        $output .= '</form>';
        $output .= '</td>';
        $output .= '</tr>';
    }
	
	$output .= '</table>';
	echo $output;
