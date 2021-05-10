<?php

    session_start();
    include('../functions.php');
    require_user_level('lector');
    include('../db.php'); /* @var mysqli $mysqli */

	// get GET parameters
	$unit_id = $_REQUEST["unitID"];
	$suggestion_type = $_REQUEST["type"];

	switch ($suggestion_type) {
        case 'lector':
            echo_lector_subsection($mysqli, $unit_id);
            break;
        case 'client':
            echo_client_subsection($mysqli, $unit_id);
            break;
        case 'unit':
            echo_unit_subsection($mysqli, $unit_id);
            break;
    }

function echo_lector_subsection($mysqli, $unit_id) {
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
}

function echo_client_subsection($mysqli, $unit_id) {
    // query DB
    $query = "SELECT * FROM unit_client JOIN client ON (unit_client.client_id = client.id) WHERE unit_client.unit_id = $unit_id AND date_leave IS NULL ";
    $query .= " AND status<>'retract' AND status<>'restrict' ORDER BY status ASC";
    $result = db_query($mysqli, $query);
    $output = '<table>';
    $has_clients = false;
    if (!is_null($result) && $result->num_rows > 0) {
        $has_clients = true;
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
                    $output .= '<input type="button" name="" class="main-form-option-button" value="Vyhodit" onClick="update_unit_client_status(this, \'' . $row['unit_id'] . '\', \'' . $row['client_id'] . '\', \'leave\');">';
                    break;
                case 'request':
                    $output .= '<input type="button" name="" class="main-form-option-button" value="Povolit" onClick="update_unit_client_status(this, \'' . $row['unit_id'] . '\', \'' . $row['client_id'] . '\', \'approve\');">';
                    $output .= '<input type="button" name="" class="main-form-option-button" value="Zamietnut" onClick="update_unit_client_status(this, \'' . $row['unit_id'] . '\', \'' . $row['client_id'] . '\', \'restrict\');">';
                    break;
                case 'invite':
                    $output .= '<input type="button" name="" class="main-form-option-button" value="Stiahnut pozvanku" onClick="update_unit_client_status(this, \'' . $row['unit_id'] . '\', \'' . $row['client_id'] . '\', \'leave\');">';
                    break;
            }

            $output .= ' </form></td>';
            $output .= '</tr>';
        }
    } else {
        // error || no clients found
        $output .= '<tr>';
        $output .= ' <td colspan="2">Bez klientov.</td>';
        $output .= '</tr>';
    }

    // option to add/invite client
    $output .= '<tr>';
    $output .= ' <td colspan="' . ($has_clients ? '3' : '2') .'">';
    $output .=   '<form autocomplete="off" class="autocomplete-form">';
    $output .=    '<div class="autocomplete">';
    $output .=     '<input class="clientSearch' . $unit_id . '" type="text" name="name" placeholder="Ferko">';
    $output .=    '</div>';
    $output .=   '</form>';
    $output .=  '</td>';
    $output .= '</tr>';

    $output .= '</table>';
    echo $output;
}

function echo_unit_subsection($mysqli, $unit_id) {
    $query = "SELECT type FROM unit WHERE id=$unit_id";
    $result = db_query($mysqli, $query);
    $type = '';
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

        // header row
        $output .= "<tr class='submerged'><th>Názov</th>";
        if ($type == 'event') $output  .= "<th>Typ</th>";
        $output .= "<th>Obsadenosť</th><th>Rgistrácia</th><th>Možnosti</th></tr>";

        while ($row = $result->fetch_assoc()) {
            $onclick = ' onclick="load_unit_details(this, ' . $row['id'] . ')" ';
            $output .= '<tr ' . $onclick . '>'; // onClick -> ajax load of unit details

            // name
            $output .= "<td class='unit_{$row['id']}_name_label'>{$row['name']}</td>";

            // type
            if ($type == 'event') {
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

            // OPTIONS
            $output .= '<td>';

            // OPTION: detach event/ocurrence from course/event
            $output .= '<form method="post" class="table-form" action="../unit-admin-overview.php" style="display: inline">';
            $output .= '<input type="hidden" name="unit_id" value="' . $row['id'] . '" />';
            $button_text = ($row['type'] == 'occurrence' ? 'Odstrániť' : 'Vyradiť zo skupiny');
            $output .= "<input type='button' value='{$button_text}' onclick='unit_detach(this, {$unit_id}, {$row['id']})' class='main-form-option-button'/>";
            $output .= '</form>';

            // OPTION: view attendance
            if (($row['type'] == 'singleevent' || $row['type'] == 'occurrence') && $row['attendance'] == '1') {
                $output .= '<form method="post" class="table-form" action="attendance-admin-overview.php" style="display: inline">';
                $output .= '<input type="hidden" name="unit_id" value="' . $row['id'] . '" />';
                $output .= '<input type="submit" name="" value="Dochádzka" class="main-form-option-button" />';
                $output .= '</form>';
            }

            // OPTION: view payments
            $output .= '<button class="main-form-option-button" onclick="event.stopPropagation(); window.location.href = \'payment-overview.php?unitID=' . $row['id'] . '\';">Platby</button>';

            // OPTION: view items
            $output .= '<button class="main-form-option-button" onclick="event.stopPropagation(); window.location.href = \'item-overview.php?unitID=' . $row['id'] . '\';">Položky</button>';

            $output .= '</td>'; // options
            $output	.= '</tr>'; // header <tr> of unit

            // tr/div to render unit_details into
            $colspan = ($type == 'event') ? 5 : 4;
            $output .= '<tr style="display: none;" class="unit_detail_container"><td colspan="' . $colspan . '"><div class="unit_detail"></div></td></tr>';
        }
    } else {
        // no events / ocurrences found
        $output .= '<tr>';
        $output .= ' <td colspan="2">No ' . ($type == 'event' ? 'events' : 'ocurrences' ) . ' found</td>';
        $output .= '</tr>';
    }

    // OPTION TO ADD OCURRENCE FOR EVENT
    if ($type == 'occurence') {
        $output .= '<tr>';
        $output .= ' <td colspan="' . ($has_output ? '5' : '1') . '">';
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
        $output .= '<input class="unitSearch' . $unit_id . '" type="text" name="name" placeholder="Vyhľadať udalosť...">';
        $output .= '</div>';
        $output .= '</form>';
        $output .= '</td>';
        $output .= '</tr>';
    }

    $output .= '</table>';
    echo $output;
}
