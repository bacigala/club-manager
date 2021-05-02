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
                    <a class="dropbtn" href="units.php">Prihlasovanie</a>
                </div>
                <div class="nav-part">
                    <a class="dropbtn" href="attendance.php">Dochádzka</a>
                </div>
                <div class="nav-part">
                    <a class="dropbtn" href="payments.php">Platby</a>
                </div>
                <?php } else { ?>

                    <?php if ($_SESSION['user_is_accountant']) { // ACCOUNTANT ?>
                    <div class="nav-part" onmouseenter="dropdownMenuHoverEnter(this)" onmouseleave="dropdownMenuHoverLeave(this)">
                        <a class="dropbtn" onclick="dropdownButtonClicked(this)" href="javascript:void(0)">Financie</a>
                        <div class="dropdown-content">
                            <a href="item-overview.php">Položky</a>
                            <a href="item-modify.php">Nová položka</a>
                            <a href="payment-overview.php">Platby</a>
                            <a href="payment-modify.php">Nová platba</a>
                        </div>
                    </div>
                    <?php } ?>

                    <?php if ($_SESSION['user_is_tutor']) {  // TUTOR (LECTOR) ?>
                    <div class="nav-part">
                        <a class="dropbtn" href="unit-admin-overview.php">Skupiny a udalosti</a>
                    </div>
                    <?php } ?>

                    <?php if ($_SESSION['user_is_admin']) { // ADMINISTRATOR ?>
                    <div class="nav-part" onmouseenter="dropdownMenuHoverEnter(this)" onmouseleave="dropdownMenuHoverLeave(this)">
                        <a class="dropbtn" onclick="dropdownButtonClicked(this)" href="javascript:void(0)">Používateľsé účty</a>
                        <div class="dropdown-content">
                            <a href="client-overview.php">Prehľad</a>
                            <a href="account-modify.php">Nový zamestnanec</a>
                            <a href="client-modify.php">Nový klinet</a>
                        </div>
                    </div>
                    <?php } ?>
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
function post_escaped($key, $return_if_not_set = '') {
	if (!isset($_POST[$key])) return $return_if_not_set;
	return addslashes(trim(strip_tags($_POST[$key])));
}
function get_escaped($key) {
    if (!isset($_GET[$key])) return '';
    return addslashes(trim(strip_tags($_GET[$key])));
}

/**
 * Verify user privilleges.
 * @param $role string client / accountant / lector / admin
 * @param $die bool whether to die if requirement is not met
 */
function require_user_level($role, $die = true) {
    $check_ok = false;
    switch ($role) {
        case 'client':
            $check_ok = isset($_SESSION['user_is_client']) && $_SESSION['user_is_client'];
            break;
        case 'accountant':
            $check_ok = isset($_SESSION['user_is_client']) && !$_SESSION['user_is_client']
                && isset($_SESSION['user_is_accountant']) && $_SESSION['user_is_accountant'];
            break;
        case 'lector':
            $check_ok = isset($_SESSION['user_is_client']) && !$_SESSION['user_is_client']
                && isset($_SESSION['user_is_tutor']) && $_SESSION['user_is_tutor'];
            break;
        case 'admin':
            $check_ok = isset($_SESSION['user_is_client']) && !$_SESSION['user_is_client']
                && isset($_SESSION['user_is_admin']) && $_SESSION['user_is_admin'];
            break;
    }
    if ($die && !$check_ok) {
        echo 'You do not have a permission to access this page.';
        die();
    }
    return $check_ok;
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

/**
 * Formats datetime from DB to datetime in input[date]
 * @param string $date_string date in DB format
 * @return string date in input[date] format
 */
function input_date_format($date_string) {
    $date = date_create($date_string);
    return date_format($date, "Y-m-d") . 'T' . date_format($date, "H:i");
}

/**
 * @return bool true if user is logged in
 */
function user_logged_in() {
    return isset($_SESSION['has_user']) && $_SESSION['has_user'];
}

/**
 * If no user if logged in, die() and display login form.
 */
function require_user_logged_in() {
    if (!user_logged_in()) {
        include('login-form.php');
        die();
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
			if (!($mysqli->query($query))) {
				$_SESSION['result_message_type'] = 'error';
				$_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať. (Chyba pri vytváraní platby.)';
				$answer = false;
			}
		} else {
			// assign all payments valid in current time
			while ($row = $result->fetch_assoc()) {
				$query  = "INSERT INTO payment SET client_id =" . $_SESSION['user_id'] . ", item_id = " . $row['id'] . ", create_datetime=NOW(), due_datetime= NOW(), amount=1, author_id=1";
				if (!($mysqli->query($query))) {
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
			$form_begin  = '<form method="post" class="table-form" action="units.php">';
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
			$form_begin  = '<form method="post" class="table-form" action="units.php">';
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
			header("Location: units.php");
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
			$output .= '	<button name="request_type" type="submit" value="modify" class="main-form-option-button">Upraviť</button>';
			$output .= '</form></td>';
			
			$output .= '</tr>';
			echo $output;
		}
		$result->free();
	}
}

?>