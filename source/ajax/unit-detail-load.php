
<?php

session_start();
include('../db.php'); /* @var mysqli $mysqli */
include('../functions.php');
require_user_level('lector');

$unit_id = $_REQUEST["q"];

$query = "SELECT * FROM unit WHERE id = $unit_id";

$result = db_query($mysqli, $query);
if (!is_null($result) && $result->num_rows > 0) {

	while ($row = $result->fetch_assoc()) {
		$output = '<table class="unit-details">';

		//name
		$output .= '<tr>';
		$output .= ' <td>Názov</td>';
		$output .= ' <td><form>';
		$output .= '  <input type="text" id="name" name="name" value="' . $row['name'] . '">';
		$output .= '  <input type="button" name="" value="Uložiť" class="main-form-option-button" onClick="update_unit_detail(this, \'' . $row['id'] . '\');">';
		$output .= ' </form></td>';
		$output .= '</tr>';
		//venue
		$output .= '<tr>';
		$output .= ' <td>Miesto</td>';
		$output .= ' <td><form>';
		$output .= '  <input type="text" id="venue" name="venue" value="' . $row['venue'] . '">';
		$output .= '  <input type="button" name="" value="Uložiť" class="main-form-option-button" onClick="update_unit_detail(this, \'' . $row['id'] . '\');">';
		$output .= ' </form></td>';
		$output .= '</tr>';
		//price
		$output .= '<tr>';
		$output .= ' <td>Cena</td>';
		$output .= ' <td><form>';
		$output .= '  <input type="number" name="price" id="price" value="' . $row['price'] . '" min="0" step="1">';
		$output .= '  <input type="button" name="" value="Uložiť" class="main-form-option-button" onClick="update_unit_detail(this, \'' . $row['id'] . '\');">';
		$output .= ' </form></td>';
		$output .= '</tr>';
		//max_clients
		$output .= '<tr>';
		$output .= ' <td>Max. účastníkov</td>';
		$output .= ' <td><form>';
		$output .= '  <input type="number" name="max_clients" id="max_clients" value="' . $row['max_clients'] . '" min="0" step="1">';
		$output .= '  <input type="button" name="" value="Uložiť" class="main-form-option-button" onClick="update_unit_detail(this, \'' . $row['id'] . '\');">';
		$output .= ' </form></td>';
		$output .= '</tr>';

		//start_datetime
		$output .= '<tr>';
		$output .= ' <td>Od</td>';
		$output .= ' <td><form>';
		$output .= '  <input type="datetime-local" id="start_datetime" name="start_datetime" value="' . input_date_format($row['start_datetime']) . '">';
		$output .= '  <input type="button" name="" value="Uložiť" class="main-form-option-button" onClick="update_unit_detail(this, \'' . $row['id'] . '\');">';
		$output .= ' </form></td>';
		$output .= '</tr>';
		//end_datetime
		$output .= '<tr>';
		$output .= ' <td>Do</td>';
		$output .= ' <td><form>';
		$output .= '  <input type="datetime-local" id="end_datetime" name="end_datetime" value="' . input_date_format($row['end_datetime']) . '">';
		$output .= '  <input type="button" name="" value="Uložiť" class="main-form-option-button" onClick="update_unit_detail(this, \'' . $row['id'] . '\');">';
		$output .= ' </form></td>';
		$output .= '</tr>';
        //registration
        $output .= '<tr>';
        $output .= ' <td>Registrácia</td>';
        $output .= ' <td><form>';
        $output .= '<select name="registration">';
        $output .= '<option value="open" ' . ($row['registration'] == 'open' ? ' selected' : '') .'>Otvorená</option>';
        $output .= '<option value="close" ' . ($row['registration'] == 'close' ? ' selected' : '') .'>Uzavretá</option>';
        $output .= '<option value="invite" ' . ($row['registration'] == 'invite' ? ' selected' : '') .'>Na pozváku</option>';
        $output .= '<option value="request" ' . ($row['registration'] == 'request' ? ' selected' : '') .'>Na požiadanie</option>';
        $output .= '</select>';
        $output .= '  <input type="button" name="" value="Uložiť" class="main-form-option-button" onClick="update_unit_detail(this, \'' . $row['id'] . '\');">';
        $output .= ' </form></td>';
        $output .= '</tr>';
		//attendance - ON/OFF
		if ($row['type'] != 'course') {
			$output .= '<tr>';
			$output .= ' <td>Dochádzka</td>';
			$output .= ' <td><form>';
            $output .= '<select id="attendance" name="attendance">';
            $output .= '<option value="1" ' . ($row['attendance'] == '1' ? ' selected' : '') .'>Zaznamenať</option>';
            $output .= '<option value="0" ' . ($row['attendance'] == '0' ? ' selected' : '') .'>Ignorovať</option>';
            $output .= '</select>';
			$output .= '  <input type="button" name="" value="Uložiť" class="main-form-option-button" onClick="update_unit_detail(this, \'' . $row['id'] . '\');">';
			$output .= ' </form></td>';
			$output .= '</tr>';
		}

		// lectors - ajax load on click
		$output .= '<tr onclick="load_unit_subsection(this, this.nextSibling,  ' . $row['id'] . ', \'lector\')" class="head-row"><td colspan="' . 2 . '">Lektori</td></tr>';
		$output .= '<tr style="display: none;" class="unit_detail_container"><td colspan="' . 2 . '"><div class="unit_detail unit' . $row['id'] . 'lectorListContainer"></div></td></tr>'; // div to render lectors

		// clients - ajax load on click
		$output .= '<tr onclick="load_unit_subsection(this, this.nextSibling,  ' . $row['id'] . ', \'client\')" class="head-row"><td colspan="' . 2 . '">Účastníci</td></tr>';
		$output .= '<tr style="display: none;" class="unit_detail_container"><td colspan="' . 3 . '"><div class="unit_detail unit' . $row['id'] . 'clientListContainer"></div></td></tr>'; // div to render clients

        if ($row['type'] == 'course') {
            // EVENTS - ajax load on click FOR COURSES
            $output .= '<tr onclick="load_unit_subsection(this, this.nextSibling, ' . $row['id'] . ' ,\'unit\' )" class="head-row"><td colspan="' . 2 . '">Udalosti</td></tr>';
            $output .= '<tr style="display: none;" class="unit_detail_container"><td colspan="' . 3 . '"><div class="unit_detail unit' . $row['id'] . 'unitListContainer"></div></td></tr>'; // div to render events of course
        }

        if ($row['type'] == 'event') {
            // OCCURENCES - ajax load on click FOR EVENTS
            $output .= '<tr onclick="load_unit_subsection(this, this.nextSibling, ' . $row['id'] . ' ,\'unit\')" class="head-row"><td colspan="' . 2 . '">Výskyty</td></tr>';
            $output .= '<tr style="display: none;" class="unit_detail_container"><td colspan="' . 3 . '"><div class="unit_detail unit' . $row['id'] . 'unitListContainer"></div></td></tr>'; // div to render ocurrences of event
        }

		$output .= '</table>';
		echo $output;
	}
	$result->free();
}

