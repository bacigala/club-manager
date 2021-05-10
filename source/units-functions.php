
<?php

// echo <tr>s of events for logged-in user of selected typee
function get_units_of_client_tr($mysqli, $type) {
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
                    $output .= '<td>' . $form_begin;
                    //$output .= '<button name="request_type" type="submit" value="leave" class="main-form-option-button">Odhlásiť sa</button>';
                    $output .=  $form_end;
                    $output .= '</td></tr>';
                    $active .= $output;
                    break;
                case 'invite':
                    $output .= 'Ste pozvaný.</td>';
                    $output .= '<td>' . $form_begin;
                    $output .= '<button name="request_type" type="submit" value="accept" class="main-form-option-button">Prijať</button>';
                    $output .= '<button name="request_type" type="submit" value="refuse" class="main-form-option-button">Odmietnuť</button>';
                    $output .=  $form_end;
                    $output .= '</td></tr>';
                    $invite .= $output;
                    break;
                case 'request':
                    $output .= 'Čaká sa na schválenie.</td>';
                    $output .= '<td>' . $form_begin;
                    $output .= '<button name="request_type" type="submit" value="retract" class="main-form-option-button">Zrušiť žiadosť</button>';
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
                        $output .= '<button name="request_type" type="submit" value="join" class="main-form-option-button">Prihlásiť sa</button>';
                        $output .=  $form_end;
                        $output .= '</td></tr>';
                        $open .= $output;
                        break;
                    case 'request':
                        $output .= '<td>registrácia na žiadosť</td>';
                        $output .= '<td>' . $form_begin;
                        $output .= '<button name="request_type" type="submit" value="request" class="main-form-option-button">Požiadať o prihlásenie</button>';
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

function record_payments($mysqli, $unit_id) {
    $answer = true;
    $query  = " SELECT * FROM item WHERE unit_id = $unit_id AND start_date>=NOW()";
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
                case 'leave':
                case 'retract':
                    $query = "UPDATE unit_client SET status='" . $request_type . "', date_leave=NOW() WHERE unit_id=" . $unit_id . " AND id=" . $unit_client_id . " AND client_id=" . $_SESSION['user_id'];
                    if (!($result = $mysqli->query($query))) {
                        $mysqli->rollback();
                        $_SESSION['result_message_type'] = 'error';
                        $_SESSION['result_message'] = 'Akciu sa nepodarilo vykonať. Žiadosť (už) neexistuje.';
                        break;
                    }

                    $mysqli->commit();
                    $_SESSION['result_message_type'] = 'success';
                    $_SESSION['result_message'] = 'Žiadosť o prihlásenie bola stiahnutá.';
                    if ($request_type == 'leave') $_SESSION['result_message'] = 'Boli ste odhlásený.';
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
                    if (!record_payments($mysqli, $unit_id)) {
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
