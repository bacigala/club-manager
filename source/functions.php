<?php

/**
 * Create html page header.
 * @param string $headline text to be shown as browser tab name
 */
function header_include($headline = 'Club manager') {
?>
	<!DOCTYPE html>
	<html lang="sk">
		<head>
			<meta charset="utf-8">
			<meta name="viewport" content="width=device-width, initial-scale=0.86, maximum-scale=3">
			<link href="https://fonts.googleapis.com/css?family=Muli&display=swap" rel="stylesheet">
			<link href="https://fonts.googleapis.com/css?family=Baloo+2&display=swap" rel="stylesheet">
			<link href="style.css" rel="stylesheet">
			<link rel="icon" type="image/png" href="images/favicon.ico">
			<title><?php echo $headline; ?></title>
		</head>
		<body>
			<header>
				<!-- if user is logged-in diplay logout option -->
				<?php if (isset($_SESSION['has_user']) && $_SESSION['has_user']) { ?>
					<div id="logout-banner">
						<form method="post" id="logout-form" action="index.php">
								<input name="logout" type="submit" id="logout" value="Odhlásiť sa">
						</form>
					</div>				
				<?php }	?>
							
				<h1><a href="index.php">Club manager</a></h1>
				<h2>Catchy slogan:)</h2>
			</header>
<?php
}

/**
 * Create html navigation.
 * @param false $full_width
 */
function nav_include($full_width = false) {
?>
    <script src="nav.js"></script>
    <nav <?php if ($full_width) echo 'class="full-width"'; ?>>
        <a id="mobile-menu-button" onclick="dropdownButtonClicked(this)" href="javascript:void(0)"><strong>MENU</strong></a>
            <div id="nav-core">
                <?php if ($_SESSION['user_is_client']) {  // CLIENT ?>
                <div class="nav-part">
                    <a class="dropbtn" href="unit-overview.php">Skupiny a udalosti</a>
                </div>
                <div class="nav-part">
                    <a class="dropbtn" href="attendance.php">Dochádzka</a>
                </div>
                <div class="nav-part">
                    <a class="dropbtn" href="payments.php">Poplatky</a>
                </div>
                <?php } ?>

                <?php if ($_SESSION['user_is_accountant']) { // ACCOUNTANT ?>
                <div class="nav-part" onmouseenter="dropdownMenuHoverEnter(this)" onmouseleave="dropdownMenuHoverLeave(this)">
                        <a class="dropbtn" onclick="dropdownButtonClicked(this)" href="javascript:void(0)">Platby</a>
                        <div class="dropdown-content">
                                <a href="payment-item-overview.php">Prehľad položiek</a>
                                <a href="payment-item-modify.php">Nová položka</a>
                                <a href="payment-overview.php">Prehľad platieb</a>
                                <a href="payment-modify.php">Nová platba</a>
                        </div>
                </div>
                <?php } ?>

                <?php if ($_SESSION['user_is_tutor']) {  // TUTOR (LECTOR) ?>
                <div class="nav-part" onmouseenter="dropdownMenuHoverEnter(this)" onmouseleave="dropdownMenuHoverLeave(this)">
                        <a class="dropbtn" onclick="dropdownButtonClicked(this)" href="javascript:void(0)">Skupiny a udalosti</a>
                        <div class="dropdown-content">
                                <a href="unit-admin-overview.php">Prehľad</a>
                                <!-- <a href="payment-item-modify.php">Nový</a> -->
                        </div>
                </div>
                <?php } ?>

                <?php if ($_SESSION['user_is_admin']) { // ADMINISTRATOR ?>
                <div class="nav-part" onmouseenter="dropdownMenuHoverEnter(this)" onmouseleave="dropdownMenuHoverLeave(this)">
                        <a class="dropbtn" onclick="dropdownButtonClicked(this)" href="javascript:void(0)">Použivatelia</a>
                        <div class="dropdown-content">
                                <a href="">Prehľad</a>
                                <a href="">Nový</a>
                        </div>
                </div>
                <?php } ?>

        </div>
        <p class="clearfix"></p>
    </nav>

<?php
}

/**
 * Retrieve escaped value from $_POST array.
 * @param $key string key in $_POST array
 * @return string escaped value $_POST[{$key}]
 */
function post_escaped($key) {
	if (!isset($_POST[$key])) return '';
	return addslashes(trim(strip_tags($_POST[$key])));
}

/**
 * Query database.
 * @param $mysqli mysqli
 * @param $query string
 * @return mysqli_result|null result of query OR null on error
 */
function db_query($mysqli, $query) {
	if (!$mysqli->connect_errno) {
        $result = $mysqli->query($query);
		if ($result) {
			return $result;
		} else {
			echo $mysqli->error;
		}
	}
    return NULL;
}

/**
 * Sets result of the operation, next page may display the result...
 * @param $type string success / warning / error
 * @param  string message to display
 */
function session_result($type, $message) {
    $_SESSION['result_message_type'] = $type;
    $_SESSION['result_message'] =  $message;
}

/**
 * Echo result stored in $_SESSION.
 * @param bool $unset true if result should be unset after echo
 */
function session_result_echo($unset = true) {
    // echo the message
    if (isset($_SESSION['result_message']) && $_SESSION['result_message'] != '') {
        $message = $_SESSION['result_message'];
        $message_type = isset($_SESSION['result_message_type']) ? $_SESSION['result_message_type'] : 'info';
        echo '<p class="' . $message_type . '">' . $message . '</p>';
    }
    // unset values
    if ($unset) {
        unset($_SESSION['result_message']);
        unset($_SESSION['result_message_type']);
    }
}






function record_payments($mysqli, $unit_id) {
	$answer = true;
	$query  = " SELECT * FROM item WHERE unit_id = $unit_id";
	$result = db_query($mysqli, $query);		
	if (!is_null($result)) {
		if ($result->num_rows == 1) {
			$row = $result->fetch_assoc();
			$query  = "INSERT INTO payment SET client_id =" . $_SESSION['user_id'] . ", item_id = " . $row['id'] . ", create_datetime=NOW(), due_datetime= NOW(), amount=1, author_id=1";
			if (!($result2 = $mysqli->query($query))) {
				$_SESSION['result_message_type'] = 'error';
				$_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať. (Chyba pri vytváraní platby.)';
				$answer = false;
			}
		} else {
			// assign all payments valid in current time
			while ($row = $result->fetch_assoc()) {
				$query  = "INSERT INTO payment SET client_id =" . $_SESSION['user_id'] . ", item_id = " . $row['id'] . ", create_datetime=NOW(), due_datetime= NOW(), amount=1, author_id=1";
				if (!($result2 = $mysqli->query($query))) {
					$_SESSION['result_message_type'] = 'error';
					$_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať. (Chyba pri vytváraní platby.)';
					$answer = false;
					break;
				}
			}
		}
        $result->free();
	} else {
		$_SESSION['result_message_type'] = 'error';
		$_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať. (Chyba pri vytváraní platby. NULL)';
		$answer = false;
	}
	return $answer;
}


function get_units_of_client($mysqli, $type) {
	$query = "SELECT unit.id, unit.name, unit.registration, unit.max_clients, unit_client.date_leave, unit_client.status, no_clients, unit_client.id AS 'unit_client_id' FROM unit JOIN unit_client ON (unit.id = unit_client.unit_id) JOIN client ON (unit_client.client_id = client.id) LEFT JOIN unit_clients ON (unit.id = unit_clients.unit_id)";
	$query .= " WHERE ";	
	$query .= " client.id = " . $_SESSION['user_id'] . " AND (date_leave IS NULL OR date_leave >= NOW()) ";
	$query .= " AND status <> 'restrict' AND status <> 'refuse' ";
	if ($type == 'course') 		$query .= " AND type = 'course'";
	if ($type == 'event')  		$query .= " AND type = 'event'";
	if ($type == 'occurence') $query .= " AND (type = 'occurrence' OR type = 'singleevent')";

	$result = db_query($mysqli, $query);		
	if (!is_null($result) && $result->num_rows > 0) {
		$active = '';
		$invite = '';
		$request = '';

		while ($row = $result->fetch_assoc()) {
			$form_begin  = '<form method="post" class="table-form" action="unit-overview.php">';
			$form_begin .= '	<input type="hidden" name="unit_id" value="' . $row['id'] . '" />';
			$form_begin .= '	<input type="hidden" name="unit_client_id" value="' .  (isset($row['unit_client_id']) ? $row['unit_client_id'] : '0') . '" />';
			$form_end = '</form>';
			
			$output  = '<tr>';
			$output .= '<td>' . $row['name'] . '</td>';
			$output .= '<td>' . (is_null($row['no_clients']) ? '0' : $row['no_clients']) . '/' . $row['max_clients'] . '</td><td>';
			
			switch ($row['status']) {
				case 'manual':
				case 'approve':
				case 'accept':
					$output .= 'Ste prihlásený.</td>';
					$output .= '<td>' . '' . '</td></tr>';
					$active .= $output;
					break;
				case 'invite':
					$output .= 'Ste pozvaný.</td>'; //todo: prijat moze len ak je este volne miesto
						$output .= '<td>' . $form_begin;
						$output .= '<button name="request_type" type="submit" value="accept">Prijať</button>';
						$output .= '<button name="request_type" type="submit" value="refuse">Odmietnuť</button>';
						$output .=  $form_end;
						$output .= '</td></tr>';
					$invite .= $output;
					break;
				case 'request':
					$output .= 'Čaká sa na schválenie.</td>';
						$output .= '<td>' . $form_begin;
						$output .= '<button name="request_type" type="submit" value="retract">Zrušiť žiadosť</button>';
						$output .=  $form_end;
						$output .= '</td></tr>';
					$request .= $output;
					break;
			}
		}
		echo $active . $invite . $request;
		$result->free();
	}
	
	// GET COURSES USER CAN JOIN / REQUEST TO JOIN
	$query  = " SELECT unit.id, unit.name, unit.registration, unit.max_clients, no_clients";
	$query .= " FROM unit LEFT JOIN unit_clients ON (unit.id = unit_clients.unit_id)";
	$query .= " WHERE ";	
	$query .= " (registration = 'open' OR registration = 'request') ";
	$query .= " AND NOT EXISTS (SELECT 1 FROM unit_client WHERE unit_client.unit_id = unit.id AND unit_client.client_id = " . $_SESSION['user_id'] . ""; // not signed for this yet
	$query .= " AND unit_client.status <> 'retract') "; // retracted == "cancelled" == do not have effect
	if ($type == 'course') 		$query .= " AND type = 'course'";
	if ($type == 'event')  		$query .= " AND type = 'event'";
	if ($type == 'occurence') $query .= " AND (type = 'occurrence' OR type = 'singleevent')";
	
	$result = db_query($mysqli, $query);		
	if (!is_null($result) && $result->num_rows > 0) {
		$open = '';
		$request = '';
		$full = '';

		while ($row = $result->fetch_assoc()) {
			$form_begin  = '<form method="post" class="table-form" action="unit-overview.php">';
			$form_begin .= '	<input type="hidden" name="unit_id" value="' . $row['id'] . '" />';
			$form_begin .= '	<input type="hidden" name="unit_client_id" value="' .  '0' . '" />'; // these records are not in unit_client table yet
			$form_end = '</form>';
			
			$output  = '<tr>';
			$output .= '<td>' . $row['name'] . '</td>';
			$output .= '<td>' . (is_null($row['no_clients']) ? '0' : $row['no_clients']) . '/' . $row['max_clients'] . '</td>';
			
			if ($row['no_clients'] >= $row['max_clients']) {
				$output .= '<td>plná kapacita :(</td>';
				$output .= '<td>' . '' . '</td></tr>';
				$full .= $output;
				
			} else {			
				switch ($row['registration']) {
					case 'open':
						$output .= '<td>registrácia otvorená</td>';
						$output .= '<td>' . $form_begin;
						$output .= '<button name="request_type" type="submit" value="join">Prihlásiť sa</button>';
						$output .=  $form_end;
						$output .= '</td></tr>';
						$open .= $output;
						break;
					case 'request':
						$output .= '<td>registrácia na žiadosť</td>';
						$output .= '<td>' . $form_begin;
						$output .= '<button name="request_type" type="submit" value="request">Požiadať o prihlásenie</button>';
						$output .=  $form_end;
						$output .= '</td></tr>';	
						$request .= $output;
						break;
				}
			}
		}
		echo $open . $request . $full;
		$result->free();
	}
}

function get_attendance_of_client($mysqli) {
	// GET UNITS USER IS SIGNED FOR AND ATTENDANCE IS RECORDED
	$query  = " SELECT unit.name, unit.start_datetime, unit.end_datetime, unit_client.present";
	$query .= " FROM unit JOIN unit_client ON (unit.id = unit_client.unit_id) JOIN client ON (unit_client.client_id = client.id) ";
	$query .= " WHERE ";	
	$query .= " client.id = " . $_SESSION['user_id'];
	$query .= " AND unit.attendance ";
	$query .= " AND status <> 'restrict' AND status <> 'refuse'  AND status <> 'invite'  AND status <> 'request' AND status <> 'retract' ";
	$query .= " AND (unit.type = 'occurrence' OR unit.type = 'singleevent')"; //only this represents attendance
	$query .= " ORDER BY unit.start_datetime ASC "; 
	
	$result = db_query($mysqli, $query);		
	if (!is_null($result) && $result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$output  = '<tr>';
			$output .= '<td>' . $row['name'] . '</td>';
			$output .= '<td>' . date_format(date_create($row['start_datetime']), "d.m.Y H:i") . ' - ' . date_format(date_create($row['end_datetime']), "d.m.Y H:i") . '</td>';
			$output .= '<td' . ($row['present'] ? '' : ' class="warn"') . '>' . ($row['present'] ? 'Prítomný(á)' : 'Neprítomný(á)') . '</td>';
			
			$output .= '</tr>';
			echo $output;
		}
		$result->free();
	}
}

function check_capacity($mysqli, $unit_id) {
	//check if there is at least one free spot
	$query = "SELECT * FROM unit LEFT JOIN unit_clients ON (unit.id = unit_clients.unit_id) WHERE unit.id=" . $unit_id;		
	if (!($result = $mysqli->query($query))) {
		$_SESSION['result_message_type'] = 'error';
		$_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať. ERROR 2345';
		return false;
	}
	
	$data = $result->fetch_assoc();
	$unit_max_capacity = $data['max_clients'];
	$unit_no_clients = isset($data['no_clients']) ? $data['no_clients'] : 0;
	
	if ($unit_max_capacity > $unit_no_clients) {
		return true;
	} else {
		// unit cannot accept more users
		$_SESSION['result_message_type'] = 'error';
		$_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať. Bol dosiahnutý maximálny počet účastníkov.';
		return false;		
	}	
}

function handle_course_request($mysqli) {
	
	if (isset($_POST['unit_id'])) {
		
		// try to fulfill user request
		$unit_id = post_escaped('unit_id');
		$request_type = post_escaped('request_type'); // accept, refuse, retract, join, request
		$unit_client_id = post_escaped('unit_client_id');

		try {
			$mysqli->begin_transaction();
			switch ($request_type) {
                case 'accept':
                case 'refuse':
                    if ($request_type == 'accept') {
                        // check available capacity
                        if (!check_capacity($mysqli, $unit_id)) {
                            $mysqli->rollback();
                            $_SESSION['result_message_type'] = 'error';
                            $_SESSION['result_message'] = 'Nemožno sa prihlásiť, limit účastníkov bol naplnený.';
                            break;
                        }

                        // record payment obligation
                        if (!record_payments($mysqli, $unit_id)) {
                            $mysqli->rollback();
                            break;
                        }
                    }
					// try to update invitation -> accept / refuse					
					$query = "UPDATE unit_client SET status='" . $request_type . "', date_join=NOW() WHERE unit_id=" . $unit_id . " AND id=" . $unit_client_id . " AND client_id=" . $_SESSION['user_id'] . " AND status='invite'";
					if (!($result = $mysqli->query($query))) {
						$mysqli->rollback();
						$_SESSION['result_message_type'] = 'error';
						$_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať. Pozvánka (už) neexistuje.';
						break;
					}
					
					$mysqli->commit();
					$_SESSION['result_message_type'] = 'success';
					$_SESSION['result_message'] = 'Pozvánka ' . ($request_type == 'accept' ? 'prijatá' : 'odmietnutá') . '.';
					break;					
				case 'retract':
					$query = "UPDATE unit_client SET status='" . $request_type . "', date_leave=NOW() WHERE unit_id=" . $unit_id . " AND id=" . $unit_client_id . " AND client_id=" . $_SESSION['user_id'] . " AND status='request'";
					if (!($result = $mysqli->query($query))) {
						$mysqli->rollback();
						$_SESSION['result_message_type'] = 'error';
						$_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať. Žiadosť (už) neexistuje.';
						break;
					}
					
					$mysqli->commit();
					$_SESSION['result_message_type'] = 'success';
					$_SESSION['result_message'] = 'Žiadosť o prihlásenie bola stiahnutá.';
					break;
				case 'join':

					// check available capacity
					if (!check_capacity($mysqli, $unit_id)) {
						$mysqli->rollback();
                        $_SESSION['result_message_type'] = 'error';
                        $_SESSION['result_message'] = 'Nemožno sa prihlásiť, limit účastníkov bol naplnený.';
						break;
					}

					// check whether unit is opened for registration and user is not already assigned to it
					$query = "SELECT * FROM unit WHERE id=" . $unit_id . " AND registration='open'";
					$query .= " AND NOT EXISTS (SELECT 1 FROM unit_client WHERE unit_client.unit_id = unit.id AND unit_client.client_id = " . $_SESSION['user_id'] . " AND date_join <= NOW() AND (date_leave IS NULL OR date_leave >= NOW()))";
					if (!($result = $mysqli->query($query)) ||  ($result->num_rows <= 0)) {
						$_SESSION['result_message_type'] = 'error';
						$_SESSION['result_message'] = 'Nepodarillo sa prihlásiť. Registrácia nie je otvorená.';
						$mysqli->rollback();
						break;
					}

					// create join record
					$query = "INSERT INTO unit_client SET unit_id=" . $unit_id . ", client_id=" . $_SESSION['user_id'] . ", author_id=1, date_join=NOW(), status='manual'";
					if (!($result = $mysqli->query($query))) {
						$_SESSION['result_message_type'] = 'error';
						$_SESSION['result_message'] = 'Nepodarillo sa prihlásiť.';
						$mysqli->rollback();
						break;
					}

					// record payment obligation
					if (!record_payments($mysqli, $unit_id)) { // wtf? stops execution
					    $mysqli->rollback();
                        $_SESSION['result_message_type'] = 'error';
                        $_SESSION['result_message'] = 'Problem pri uctovani platby.';

						break;					
					}


					$mysqli->commit();
					$_SESSION['result_message_type'] = 'success';
					$_SESSION['result_message'] = 'Prihlásenie bolo úspešné.';
					break;		
				case 'request':	
					// check available capacity
					if (!check_capacity($mysqli, $unit_id)) {
						$mysqli->rollback();
						break;
					}
					// check whether unit is opened for request-registration and user is not already assigned to it
					$query = "SELECT * FROM unit WHERE id=" . $unit_id . " AND registration='request'";
					$query .= " AND NOT EXISTS (SELECT 1 FROM unit_client WHERE unit_client.unit_id = unit.id AND unit_client.client_id = " . $_SESSION['user_id'] . " AND date_join <= NOW() AND (date_leave IS NULL OR date_leave >= NOW()))";
					if (!($result = $mysqli->query($query)) ||  ($result->num_rows <= 0)) {
						$_SESSION['result_message_type'] = 'error';
						$_SESSION['result_message'] = 'Žiadaosť sa nepodarilo vytvoriť. Nemožno podávať žiadosti.';
						$mysqli->rollback();
						break;
					}					
					// create request record
					$query = "INSERT INTO unit_client SET unit_id=" . $unit_id . ", client_id=" . $_SESSION['user_id'] . ", author_id=1, date_join=NOW(), status='request'";
					if (!($result = $mysqli->query($query))) {
						$mysqli->rollback();
						$_SESSION['result_message_type'] = 'error';
						$_SESSION['result_message'] = 'Žiadaosť sa nepodarilo vytvoriť.';
						break;
					}
					
					$mysqli->commit();
					$_SESSION['result_message_type'] = 'success';
					$_SESSION['result_message'] = 'Žiadosť o prihlásenie bola odoslaná.';
					break;	
				
			}	
		} catch (mysqli_sql_exception $exception) {
			// error
			$mysqli->rollback();
			// set result message
			$_SESSION['result_message_type'] = 'error';
			$_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať. (exception)' . $exception;
		} finally {
			// restrict form resend by refresh, clear $_POST
			//$_SESSION['result_message'] .= $mysqli->error;
			header("Location: unit-overview.php");
			exit();
		}
	}

	// echo result of previous operation (info banner)
	session_result_echo();
}

function get_payment_list($mysqli) {
	$query  = " SELECT * FROM payment JOIN item ON (payment.item_id = item.id) WHERE payment.client_id = " . $_SESSION['user_id'] . " ORDER BY create_datetime DESC";
	$result = db_query($mysqli, $query);		
	if (!is_null($result) && $result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$output  = '<tr>';
			$output .= '<td>' . $row['name'] . '</td>';
			$output .= '<td>' . date_format(date_create($row['create_datetime']), "d.m.Y H:i") . '</td>';
			$output .= '<td>' . $row['amount'] . '</td>';
			$output .= '<td>' . $row['price'] * $row['amount'] . '</td>';
			$output .= "<td " . (isset($row['pay_datetime']) ? "" : "class='warn'") . ">" . (isset($row['pay_datetime']) ? date_format(date_create($row['pay_datetime']), "d.m.Y H:i") : 'neuhradené') . '</td>';
			$output .= '</tr>';
			echo $output;
		}
		$result->free();
	}
}

function get_item_list($mysqli) {
	$query  = " SELECT item.id, item.name, item.price, item.start_date, item.end_date, unit.name AS 'unit_name' FROM item LEFT JOIN unit ON (item.unit_id = unit.id) ORDER BY name ASC, start_date DESC";
	$result = db_query($mysqli, $query);		
	if (!is_null($result) && $result->num_rows > 0) {				
		while ($row = $result->fetch_assoc()) {
			$output  = '<tr>';
			$output .= '<td>' . $row['name'] . '</td>';
			$output .= '<td>' . number_format($row['price'], 2, ',', ' ') . ' €</td>';
			$output .= '<td>' . (isset($row['unit_name']) ? $row['unit_name'] : "")  . '</td>';
			$output .= '<td>' . (isset($row['start_date']) ? date_format(date_create($row['start_date']), "d.m.Y") : "")  . '</td>';
			$output .= '<td>' . (isset($row['end_date']) ? date_format(date_create($row['end_date']), "d.m.Y") : "")  . '</td>';
			
			// options
			$output .= '<td><form method="post" class="table-form" action="payment-item-modify.php">';
			$output .= '	<input type="hidden" name="item_id" value="' . $row['id'] . '" />';
			$output .= '	<button name="request_type" type="submit" value="modify">Upraviť</button>';
			$output .= '</form></td>';
			
			$output .= '</tr>';
			echo $output;
		}
		$result->free();
	}
}

function get_item($mysqli, $item_id = 0) {
	$query  = " SELECT * FROM item WHERE id = $item_id";
	$result = db_query($mysqli, $query);
	$return_value = '';
	if (!is_null($result) && $result->num_rows > 0) {
        if ($row = $result->fetch_assoc()) {
            $return_value = $row;
        }
        $result->free();
    }
	return $return_value;
}

function get_unit_options($mysqli, $selected = false) {
	$query  = "SELECT unit.id, unit.name, unit.type FROM unit ORDER BY type ASC, name ASC";
	$result = db_query($mysqli, $query);		
	if (!is_null($result) && $result->num_rows > 0) {				
		while ($row = $result->fetch_assoc()) {
			$type = '';
			switch ($row['type']) {
				case 'course':
					$type = ' (kurz)';
					break;
				case 'event':
				case 'singleevent':
					$type = ' (udalosť)';
					break;
				case 'occurrence':
					$type = ' (výskyt)';
					break;					
			}
			
			$output = '<option value="' . $row['id'] . '" ';
			if ($selected && $selected == $row['id']) $output .= 'selected';
			$output .= '>' . $row['name'] . $type . '</option>';
		
			echo $output;
		}
		$result->free();
	}
}

function get_item_form($mysqli, $type = 'create', $form_data = false) {
	// get item id
	if (isset($_POST['request_type'])) $type = post_escaped('request_type');
	if (!$form_data) {
		if (isset($_POST['item_id'])) {
			if ($item = get_item($mysqli, post_escaped('item_id'))) {
				// display form for item modification
				$form_data = $item;
			} else {
				// requested item does not exist
				echo "<p class='error'>Requested item does not exist.</p>";
				return;
			}	
		}
	}
	
	// display form
	?>
	<form method="post" class="master-form">
		<fieldset>
			<legend>Položka</legend>
			
			<input type="hidden" name="item_id" value="<?php if (isset($_POST['item_id'])) echo post_escaped('item_id'); ?>"/>
			
			<label for="name" class="required">Názov</label>
			<input type="text" name="name" id="name" maxlength="40" value="<?php if (isset($form_data['name'])) echo $form_data['name']; ?>">
		
			<label for="price" class="required">Cena</label>
			<input type="number" name="price" id="price" value="<?php if (isset($form_data['price'])) echo $form_data['price']; ?>" min="0" step="0.01">
			
			<label for="delay" class="required">Zaplatiť do (počet dní)</label>
			<input type="number" name="delay" id="delay" value="<?php if (isset($form_data['delay'])) echo $form_data['delay']; ?>" min="0" step="1">
		</fieldset>
		
		<fieldset>
			<legend>Asociovaná udalosť / kurz</legend>
			
			<label for="unit_id">Udalosť</label>
			<select id="unit_id" name="unit_id">
				<option value="0" <?php if (!isset($form_data['unit_id'])) echo 'selected';?> >Žiadna</option>
				<?php
					$unit_id = isset($form_data['unit_id']) ? $form_data['unit_id'] : false;
					get_unit_options($mysqli, $unit_id);
				?>
			</select>
			
			<label for="start_date" class="required">Od</label>
			<input type="date" name="start_date" id="start_date" min="1900-01-01" value="<?php if (isset($form_data['start_date'])) echo $form_data['start_date']; ?>">
						
			<label for="end_date" class="required">Do</label>
			<input type="date" name="end_date" id="end_date" min="1900-01-01" value="<?php if (isset($form_data['end_date'])) echo $form_data['end_date']; ?>">
		</fieldset>
		
		<fieldset>
			<legend>Potvrdenie</legend>
			<?php if ($type == 'create') { ?>
				<button name="item_create" type="submit">Vytvoriť položku</button>
				<button name="item_cancel" type="submit">Zrušiť</button>
			<?php } ?>
			<?php if ($type == 'modify') { ?>
				<button name="item_modify" type="submit">Uložiť zmeny</button>
				<button name="item_cancel" type="submit">Zrušiť zmeny</button>
				<button name="item_delete" type="submit">Odstrániť položku</button>
			<?php } ?>				
		</fieldset>
	</form>
	<?php	
}

function get_item_form_data() {
	
	// get & verify data
	$data = array();
	$error = array();
	
	if (isset($_POST['name'])) {
		$data['name'] = post_escaped('name');
		if ($data['name'] == '') $error['name'] = 'Prosím zadajte názov.';
	}
	
	if (isset($_POST['price'])) {
		$data['price'] =  intval(post_escaped('price'));
		if ($data['price'] < 0) $error['price'] = 'Cena musí byť nezáporná.';
	}
	
	if (isset($_POST['start_date'])) $data['start_date'] = post_escaped('start_date');
	if (isset($_POST['end_date'])) $data['end_date'] =  post_escaped('end_date');
	if (isset($_POST['unit_id']) && post_escaped('unit_id')) $data['unit_id'] = post_escaped('unit_id');
	
	$data['delay'] = isset($_POST['delay']) ? post_escaped('delay') : '';	
	
	// return data
	$_SESSION['error'] = $error;
	return $data;
}

function handle_item_modify($mysqli) {
	$request_type = '';
	if (isset($_POST['item_create'])) $request_type = 'create';
	if (isset($_POST['item_cancel'])) $request_type = 'cancel';
	if (isset($_POST['item_modify'])) $request_type = 'modify';
	if (isset($_POST['item_delete'])) $request_type = 'delete';
	
	$item_id = 0;
	if (isset($_POST['item_id'])) $item_id = intval(post_escaped('item_id'));

    if ($request_type) {
		
		// try to fulfill user request
		//$unit_id = post_escaped('unit_id');
		//$request_type = post_escaped('request_type'); // accept, refuse, retract, join, request
		//$unit_client_id = post_escaped('unit_client_id');
		
		try {
			switch ($request_type) {
				case 'create':	
					// create item
					$data = get_item_form_data();
					if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
						$_SESSION['result_message_type'] = 'error';
						$_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať.';
						break;
					}
					
					$mysqli->begin_transaction();
					$query = "INSERT INTO item SET author_id=" . $_SESSION['user_id'];
					foreach ($data AS $key => $value) $query .= ", $key='$value'";
					if (!$mysqli->query($query)) {
						$mysqli->rollback();
						$_SESSION['result_message_type'] = 'error';
						$_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať.';
						break;
					}
					
					$mysqli->commit();
					$_SESSION['result_message_type'] = 'success';
					$_SESSION['result_message'] = 'Položka bola vytvorená.';
					break;
				case 'cancel':
					$_SESSION['result_message_type'] = 'warning';
					$_SESSION['result_message'] = 'Položka nebola upravená.';
					break;					
				case 'modify':
					// modify item
					$data = get_item_form_data();
					if ((isset($_SESSION['error']) && !empty($_SESSION['error'])) || !$item_id) {
						$_SESSION['result_message_type'] = 'error';
						$_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať.';
						break;
					}
					
					$mysqli->begin_transaction();
					$query = "UPDATE item SET author_id=" . $_SESSION['user_id'];
					foreach ($data AS $key => $value) $query .= ", $key='$value'";
					$query .= ' WHERE id=' . $item_id ;
					if (!$mysqli->query($query)) {
						$mysqli->rollback();
						$_SESSION['result_message_type'] = 'error';
						$_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať.';
						break;
					}
					
					$mysqli->commit();
					$_SESSION['result_message_type'] = 'success';
					$_SESSION['result_message'] = 'Položka bola upravená.';
					break;
				case 'delete':			
					// delete item					
					$mysqli->begin_transaction();
					$query = "DELETE FROM item WHERE id=" . $item_id ;
					if (!$mysqli->query($query)) {
						$mysqli->rollback();
						$_SESSION['result_message_type'] = 'error';
						$_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať.';
						break;
					}
					
					$mysqli->commit();
					$_SESSION['result_message_type'] = 'success';
					$_SESSION['result_message'] = 'Položka bola odstránená.';
					break;				
			}	
		} catch (mysqli_sql_exception $exception) {
			// error
			$mysqli->rollback();
			// set result message
			$_SESSION['result_message_type'] = 'error';
			$_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať. (exception)' . $exception;
		} finally {
			$_SESSION['result_message'] .= $mysqli->error;
			header("Location: payment-item-modify.php");
			exit();
		}
	}
	
	
	if (isset($_SESSION['result_message']) && $_SESSION['result_message'] != '') {
		$message = $_SESSION['result_message'];
		$message_type = isset($_SESSION['result_message_type']) ? $_SESSION['result_message_type'] : 'info';
		
		unset($_SESSION['result_message']);
		unset($_SESSION['result_message_type']);
		
		echo '<p class="' . $message_type . '">' . $message . '</p>' ;
	}
	
	if (isset($_SESSION['error'])) {
		foreach ($_SESSION['error'] AS $value) {
			echo '<p class="error">' . $value . '</p>' ;
		}
		unset($_SESSION['error']);
	}
	
	
	get_item_form($mysqli);
}

function get_all_payments($mysqli) {
    $highlight_pid = isset($_GET['pid']) ? $_GET['pid'] : 0;

	$query  = " SELECT payment.id, payment.create_datetime, client.name, client.surname, item.name AS 'item_name', payment.amount, payment.unit_price, payment.pay_datetime FROM payment JOIN item ON (payment.item_id = item.id) JOIN client ON (client.id = payment.client_id) ORDER BY create_datetime DESC";
	$result = db_query($mysqli, $query);		
	if (!is_null($result) && $result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {
			$output  = '<tr' . ($highlight_pid == $row['id'] ? ' class="highlight"' : '') . '>';
			$output .= '<td>' . date_format(date_create($row['create_datetime']), "d.m.Y H:i") . '</td>';
			$output .= '<td>' . $row['name'] . ' ' . $row['surname'] . '</td>';
			$output .= '<td>' . $row['item_name'] . '</td>';
			$output .= '<td>' . $row['amount'] . '</td>';
			$output .= '<td>' . $row['amount'] * $row['unit_price'] . '</td>';
			$output .= "<td " . (isset($row['pay_datetime']) ? "" : "class='warn'") . ">" . (isset($row['pay_datetime']) ? date_format(date_create($row['pay_datetime']), "d.m.Y H:i") : 'neuhradené') . '</td>';
			
			// options
			$output .= '<td><form method="post" class="table-form" action="payment-modify.php">';
			$output .= '	<input type="hidden" name="payment_id" value="' . $row['id'] . '" />';
			$output .= '	<button name="request_type" type="submit" value="modify">Upraviť</button>';
			$output .= '</form></td>';
			
			$output .= '</tr>';
			echo $output;
		}
		$result->free();
	}
}

?>