<?php

/* DB payment manipulation */

function db_payment_select($mysqli, $item_id = 0) {
    $query  = "SELECT * FROM payment";
    if ($item_id > 0)  $query .= " WHERE id = $item_id";
    $result = db_query($mysqli, $query);
    $return_val = false;
    if (!is_null($result) && $result->num_rows > 0) {
        if ($row = $result->fetch_assoc()) {
            $return_val = $row;
        }
        if ($item_id == 0) return $result;
        $result->free();
    }
    return $return_val;
}

function db_payment_insert($mysqli, $payment_data, $author_id = false) {
    if (!$author_id) $author_id = $_SESSION['user_id'];
    $query = "INSERT INTO payment SET create_datetime=NOW(), author_id=$author_id";
    foreach ($payment_data AS $key => $value) {
        if ($value == '')
            $query .= ", $key=NULL"; // empty string = NULL in DB
        else
            $query .= ", $key='$value'";
    }
    return $mysqli->query($query);
}

function db_payment_update($mysqli, $payment_data, $payment_id, $author_id = false) {
    if (!$author_id) $author_id = $_SESSION['user_id'];
    $query = "UPDATE payment SET author_id=$author_id";
    foreach ($payment_data AS $key => $value) {
        if ($value == '')
            $query .= ", $key=NULL"; // empty string = NULL in DB
        else
            $query .= ", $key='$value'";
    }
    $query .= ' WHERE id=' . $payment_id ;
    return $mysqli->query($query);
}

function db_payment_delete($mysqli, $payment_id) {
    $query = "DELETE FROM payment WHERE id=$payment_id";
    return $mysqli->query($query);
}


/*
 * Generate <tr>s for all-payments overview on payment-overview.php (for accountant)
 */
function get_all_payments(mysqli $mysqli, $unit_id = false, $client_id = false) {
    $highlight_pid = intval(get_escaped('pid'));

    $query  = "SELECT payment.id, payment.create_datetime, client.name, client.surname, item.name AS 'item_name', payment.amount, payment.unit_price, payment.pay_datetime"
            . " FROM payment JOIN item ON (payment.item_id = item.id) JOIN client ON (client.id = payment.client_id) "
            . " WHERE TRUE "
            . ($unit_id ? " AND unit_id='$unit_id' " : "")
            . ($client_id ? " AND client_id='$client_id' " : "")
            . " ORDER BY create_datetime DESC";
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
            $output .= '<input type="hidden" name="payment_id" value="' . $row['id'] . '" />';
            $output .= '<button name="request_type" type="submit" value="modify" class="main-form-option-button">Upraviť</button>';
            $output .= '</form></td>';

            $output .= '</tr>';
            echo $output;
        }
        $result->free();
    }
}


/**
 * Creates content of payment-modify.php accountant page.
 * @param $mysqli
 */
function handle_payment_modify($mysqli) {

    // set by modify-caller OR sent form
    $payment_id = intval(post_escaped('payment_id'));

    // set by payment-modify-form
    $request_type = false;
    if (isset($_POST['item_create'])) $request_type = 'create';
    if (isset($_POST['item_cancel'])) $request_type = 'cancel';
    if (isset($_POST['item_modify'])) $request_type = 'modify';
    if (isset($_POST['item_delete'])) $request_type = 'delete';

    // form sent
    if ($request_type) {
        try {
            $data = array();
            switch ($request_type) {
                case 'create':
                    // get & verify filled-in dataa
                    $data = get_payment_form_data();
                    if (!empty($_SESSION['error'])) {
                        $_SESSION['data'] = $data; // store data to be pre-filled after reload
                        break;
                    }
                    // DB INSERT
                    if (db_payment_insert($mysqli, $data))
                        session_result('success', 'Platba bola vytvorená.');
                    else
                        session_result('error', 'Platba nebola vytvorená. (DB error)');
                    break;
                case 'cancel':
                    session_result('warning', 'Platba nebola vytvorená/upravená.');
                    header("Location: payment-overview.php?pid=" . $payment_id);
                    exit();
                case 'modify':
                    $data = get_payment_form_data();
                    if (!empty($_SESSION['error'])) {
                        $_SESSION['data'] = $data; // store data to be pre-filled after reload
                        break;
                    }
                    if (!db_payment_update($mysqli, $data, $payment_id))
                        session_result('error', 'Akciu sa nepodarilo vykonať. 03');
                    else
                        session_result('success', 'Platba bola upravená.');
                    break;
                case 'delete':
                    if (!db_payment_delete($mysqli, $payment_id))
                        session_result('error', 'Akciu sa nepodarilo vykonať. (DB Error)');
                    else
                        session_result('success', 'Platba bola odstránená.');
                    break;
            }
        } catch (mysqli_sql_exception $exception) {
            session_result('error', 'Akciu sa nepodarilo vykonať. (DB Fatal) ' . $exception);
        } finally {
            if ($_SESSION['result_message_type'] == 'error' || !empty($_SESSION['error'])) {
                // errors -> stay on this page
                $_SESSION['data'] = $data;
                $_SESSION['result_message'] .= $mysqli->error;
                header("Location: payment-modify.php");
            } else {
                unset($_SESSION['data']);
                unset($_SESSION['error']);
                header('Location: payment-overview.php');
            }
            exit();
        }
    }

    // show form
    if (isset($_SESSION['error'])) {
        // errors set -> unsucessfully sent form -> display errors and filled values
        echo '<p class="error">Formulár nebol odoslaný.</p>';
        foreach ($_SESSION['error'] AS $value)
            echo '<p class="error">' . $value . '</p>' ;
        unset($_SESSION['error']);
        get_payment_form($mysqli, $_SESSION['data'], $request_type);
        unset($_SESSION['data']);
    } else {
        // show form (fresh OR load data by payment_id)
        get_payment_form($mysqli, false, $payment_id?"modify":"create", $payment_id);
    }
}


/*
 * Return validated form data OR false on error, put errors in $_SESSION['error'] array
 */
function get_payment_form_data() {
    $data = array();
    $error = array();

    // item
    $data['item_id'] = intval(post_escaped('item_id'));
    if ($data['item_id'] < 0) $error['item_id'] = 'Prosím zvoľte položku.';

    // client
    $data['client_id'] =  intval(post_escaped('client_id'));
    if ($data['client_id'] < 1) $error['client_id'] = 'Prosím zvoľte platcu.';

    // amount
    $data['amount'] =  intval(post_escaped('amount'));
    if ($data['amount'] < 1) $error['client_id'] = 'Prosím zvoľte počet.';

    // status dates
    switch (post_escaped('pay_datetime_switch')) {
        case 'wait':
            // awaiting payment -> due_datetime might be set
            $data['due_datetime'] = post_escaped('due_datetime');
            $data['type'] = '';         // set to NULL in DB
            $data['pay_datetime'] = ''; // set to NULL in DB
            break;
        case 'done':
            // payment done -> date and method should be set
            if (post_escaped('pay_datetime') != '') {
                $data['pay_datetime'] =  post_escaped('pay_datetime');
            } else {
                $error['pay_datetime'] = "Prosím vyberte čas registrácie platby.";
            }
            $data['type'] = post_escaped('type');
            break;
        default:
            $error['payment_wait_switch'] = 'Prosím vyberte stav platby.';
    }

    // if data are not being send to DB (due to an error), these are used to pre-fill form again
    if (!empty($error)) {
        $data['pay_datetime_switch'] = post_escaped('pay_datetime_switch');
        $data['payment_id'] = post_escaped('payment_id');
    }

    // return data
    $_SESSION['error'] = $error;
    return $data;
}


/*
 * Echo form for payment modification / creation
 */
function get_payment_form($mysqli, $form_data = false, $type = 'create', $payment_id = 0) {
    if (!$form_data && $type == 'modify') {
        // try to fetch data from DB
        if ($item = db_payment_select($mysqli, $payment_id)) {
            // display form for item modification
            $form_data = $item;
            $form_data['payment_id'] = $payment_id;

            // decide if payment is done or waiting
            $form_data['pay_datetime_switch'] = (isset($form_data['pay_datetime'])) ? 'done' : 'wait';
        } else {
            // requested item does not exist
            echo "<p class='error'>Zvolená platba (už) neexituje.</p>";
            return;
        }
    }

    // display form
    ?>
    <form method="post" class="master-form">

        <fieldset>
            <legend>Položka</legend>

            <input type="hidden" name="payment_id" value="<?php if (isset($form_data['payment_id'])) echo $form_data['payment_id']; ?>"/>

            <label for="item_id">Položka</label>
            <select id="item_id" name="item_id">
                <?php
                    $item_id = isset($form_data['item_id']) ? $form_data['item_id'] : false;
                    get_item_options($mysqli, $item_id);
                ?>
            </select>

            <label for="amount" class="required">Počet</label>
            <input type="number" name="amount" id="amount" value="<?php if (isset($form_data['amount'])) echo $form_data['amount']; ?>" min="1" step="1">
        </fieldset>

        <fieldset>
            <legend>Platca</legend>

            <label for="client_id">Klient</label>
            <select id="client_id" name="client_id">
                <?php
                    $unit_id = isset($form_data['client_id']) ? $form_data['client_id'] : false;
                    get_client_options($mysqli, $unit_id);
                ?>
            </select>
        </fieldset>

        <fieldset>
            <legend>Stav</legend>

            <div class="form-component-container">
                <input type="radio" id="pay_datetime_ignore" name="pay_datetime_switch" value="wait" onclick="payment_wait()" <?php if (isset($form_data['pay_datetime_switch']) && $form_data['pay_datetime_switch'] == 'wait')  echo 'checked'; ?>>
                <label for="pay_datetime_ignore">Neuhradená</label><br>

                <input type="radio" id="pay_datetime_record" name="pay_datetime_switch" value="done" onclick="payement_record()" <?php if (isset($form_data['pay_datetime_switch']) && $form_data['pay_datetime_switch'] == 'done')  echo 'checked'; ?>>
                <label for="pay_datetime_record">Uhradená</label><br>
            </div>

            <div id="payment-wait-detail" class="form-subsection-show-hide" style="display: <?php echo (isset($form_data['pay_datetime_switch']) && $form_data['pay_datetime_switch'] == 'wait') ? 'block' : 'none'; ?>;">
                <label for="due_datetime">Zaplatiť do</label>
                <input type="datetime-local" id="due_datetime" name="due_datetime" value="<?php if (isset($form_data['due_datetime'])) echo input_date_format($form_data['due_datetime']); ?>">
            </div>

            <div id="payment-record-detail" class="form-subsection-show-hide" style="display: <?php echo (isset($form_data['pay_datetime_switch']) && $form_data['pay_datetime_switch'] == 'done') ? 'block' : 'none'; ?>;">
                <label for="pay_datetime">Čas prijatia platby</label>
                <input type="datetime-local" id="pay_datetime" name="pay_datetime" value="<?php if (isset($form_data['pay_datetime'])) echo input_date_format($form_data['pay_datetime']); ?>">

                <label for="type">Forma</label>
                <select id="type" name="type">
                    <?php
                        $pay_type = isset($form_data['type']) ? $form_data['type'] : false;
                        $types = array("cash","ib");
                        foreach ($types as $t) {
                            echo("<option value='$t'");
                            if ($pay_type == $t) echo ' selected';
                            echo ">$t</option>";
                        }
                    ?>
                </select>
            </div>
        </fieldset>

        <fieldset>
            <legend>Potvrdenie</legend>
            <?php if ($type == 'create') { ?>
                <button name="item_create" type="submit">Vytvoriť platbu</button>
                <button name="item_cancel" type="submit">Zrušiť</button>
            <?php } else { ?>
                <button name="item_modify" type="submit">Uložiť zmeny</button>
                <button name="item_cancel" type="submit">Zrušiť zmeny</button>
                <button name="item_delete" type="submit">Odstrániť platbu</button>
            <?php } ?>
        </fieldset>
    </form>
    <?php
}


function get_item_options($mysqli, $selected = false) {
    $query  = "SELECT id, name FROM item ORDER BY name ASC";
    $result = db_query($mysqli, $query);
    if (!is_null($result) && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $output = '<option value="' . $row['id'] . '" ';
            if ($selected && ($selected == $row['id'])) $output .= 'selected';
            $output .= '>' . $row['name'] . '</option>';
            echo $output;
        }
        $result->free();
    }
}

function get_client_options($mysqli, $selected = false) {
    $query  = "SELECT id, name FROM client ORDER BY name ASC";
    $result = db_query($mysqli, $query);
    if (!is_null($result) && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $output = '<option value="' . $row['id'] . '" ';
            if ($selected && $selected == $row['id']) $output .= 'selected';
            $output .= '>' . $row['name'] . '</option>';
            echo $output;
        }
        $result->free();
    }
}

function echo_unit_info_header($mysqli, $unit_id = false, $client_id = false) {
    if (!$unit_id && !$client_id) return;

    if ($unit_id) {
        $query = "SELECT * FROM unit WHERE id='$unit_id'";
        $result = db_query($mysqli, $query);
        if (!is_null($result) && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $output = "<h2>";
                $output .= "Zobrazujem platby pre " . translate_unit_type($row['type']) . " " . $row['name'];
                $output .= '(' . date_format(date_create($row['start_datetime']), "d.m.Y H:i");
                $output .= ' - ' . date_format(date_create($row['end_datetime']), "d.m.Y H:i") . ')';
                $output .= '</h2>';
                echo $output;
            }
            $result->free();
        }
    }

    if ($client_id) {
        $query = "SELECT * FROM client WHERE id='$client_id'";
        $result = db_query($mysqli, $query);
        if (!is_null($result) && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $output = "<h2>";
                $output .= "Zobrazujem platby pre používateľa " . $row['name'] . " " . $row['surname'];
                $output .= '</h2>';
                echo $output;
            }
            $result->free();
        }
    }
}