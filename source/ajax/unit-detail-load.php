
<?php

function input_date_format($date_string) {
	$date = date_create($date_string);
	return date_format($date, "Y-m-d") . 'T' . date_format($date, "H:i");
}


// user needs to be tutor
session_start();
if (!isset($_SESSION['user_is_tutor']) || !$_SESSION['user_is_tutor']) {
	echo 'Na prístup k tejto stránke nemáte oprávnenie.';
	die();
}

// echo unit details

include('../db.php');
include('../functions.php');

$q = $_REQUEST["q"];

$query = "SELECT * FROM unit WHERE id = $q";


$result = db_query($mysqli, $query);
if (!is_null($result) && $result->num_rows > 0) {

	while ($row = $result->fetch_assoc()) {
		$output = '<table>';

		//name
		$output .= '<tr>';
		$output .= ' <td>Názov</td>';
		$output .= ' <td><form>';
		$output .= '  <input type="text" id="name" name="name" value="' . $row['name'] . '">';
		$output .= '  <input type="button" name="" value="Uložiť" onClick="update_unit_detail(this, \'' . $row['id'] . '\');">';
		$output .= ' </form></td>';
		$output .= '</tr>';
		//venue
		$output .= '<tr>';
		$output .= ' <td>Miesto</td>';
		$output .= ' <td><form>';
		$output .= '  <input type="text" id="venue" name="venue" value="' . $row['venue'] . '">';
		$output .= '  <input type="button" name="" value="Uložiť" onClick="update_unit_detail(this, \'' . $row['id'] . '\');">';
		$output .= ' </form></td>';
		$output .= '</tr>';
		//price
		$output .= '<tr>';
		$output .= ' <td>Cena</td>';
		$output .= ' <td><form>';
		$output .= '  <input type="number" name="price" id="price" value="' . $row['price'] . '" min="0" step="1">';
		$output .= '  <input type="button" name="" value="Uložiť" onClick="update_unit_detail(this, \'' . $row['id'] . '\');">';
		$output .= ' </form></td>';
		$output .= '</tr>';
		//max_clients
		$output .= '<tr>';
		$output .= ' <td>Max. účastníkov</td>';
		$output .= ' <td><form>';
		$output .= '  <input type="number" name="max_clients" id="max_clients" value="' . $row['max_clients'] . '" min="0" step="1">';
		$output .= '  <input type="button" name="" value="Uložiť" onClick="update_unit_detail(this, \'' . $row['id'] . '\');">';
		$output .= ' </form></td>';
		$output .= '</tr>';
		//registration
		$output .= '<tr>';
		$output .= ' <td>Registrácia</td>';
		$output .= ' <td><form>';
		$output .= '  <input type="text" id="registration" name="registration" value="' . $row['registration'] . '">';
		$output .= '  <input type="button" name="" value="Uložiť" onClick="update_unit_detail(this, \'' . $row['id'] . '\');">';
		$output .= ' </form></td>';
		$output .= '</tr>';
		//start_datetime
		$output .= '<tr>';
		$output .= ' <td>Od</td>';
		$output .= ' <td><form>';
		$output .= '  <input type="datetime-local" id="start_datetime" name="start_datetime" value="' . input_date_format($row['start_datetime']) . '">';
		$output .= '  <input type="button" name="" value="Uložiť" onClick="update_unit_detail(this, \'' . $row['id'] . '\');">';
		$output .= ' </form></td>';
		$output .= '</tr>';
		//end_datetime
		$output .= '<tr>';
		$output .= ' <td>Do</td>';
		$output .= ' <td><form>';
		$output .= '  <input type="datetime-local" id="end_datetime" name="end_datetime" value="' . input_date_format($row['end_datetime']) . '">';
		$output .= '  <input type="button" name="" value="Uložiť" onClick="update_unit_detail(this, \'' . $row['id'] . '\');">';
		$output .= ' </form></td>';
		$output .= '</tr>';
		//attendance - ON/OFF
		if ($row['type'] != 'course') {
			$output .= '<tr>';
			$output .= ' <td>Dochádzka</td>';
			$output .= ' <td><form>';
			$output .= '  <input type="text" id="attendance" name="attendance" value="' . $row['attendance'] . '">';
			$output .= '  <input type="button" name="" value="Uložiť" onClick="update_unit_detail(this, \'' . $row['id'] . '\');">';
			$output .= ' </form></td>';
			$output .= '</tr>';
		}

		// lectors - ajax load on click
		$output .= '<tr onclick="load_unit_lectors(this, this.nextSibling,  ' . $row['id'] . ')" class="head-row"><td colspan="' . 2 . '">Lektori</td></tr>';
		$output .= '<tr style="display: none;"><td colspan="' . 2 . '"><div class="unit_detail unit' . $row['id'] . 'lectorListContainer"></div></td></tr>'; // div to render lectors

		// clients - ajax load on click
		$output .= '<tr onclick="load_unit_clients(this, ' . $row['id'] . ')" class="head-row"><td colspan="' . 2 . '">Účastníci</td></tr>';
		$output .= '<tr style="display: none;"><td colspan="' . 3 . '"><div class="unit_detail unit' . $row['id'] . 'clientListContainer"></div></td></tr>'; // div to render clients

		$output .= '</table>';
		echo $output;
	}
	$result->free();
}


// <form action=""> 
  // <select name="customers" onchange="showCustomer(this.value)">
    // <option value="">Select a customer:</option>
    // <option value="ALFKI">Alfreds Futterkiste</option>
    // <option value="NORTS ">North/South</option>
    // <option value="WOLZA">Wolski Zajazd</option>
  // </select>
// </form>
