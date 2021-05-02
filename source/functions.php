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
