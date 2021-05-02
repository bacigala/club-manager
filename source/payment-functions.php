<?php

/**
 * Creates content of payment-modify.php accountant page.
 * @param $mysqli
 */
function handle_payment_modify($mysqli) {
    $request_type = '';
    if (isset($_POST['item_create'])) $request_type = 'create';
    if (isset($_POST['item_cancel'])) $request_type = 'cancel';
    if (isset($_POST['item_modify'])) $request_type = 'modify';
    if (isset($_POST['item_delete'])) $request_type = 'delete';

    $payment_id = 0;
    if (isset($_POST['payment_id'])) $payment_id = intval(post_escaped('payment_id'));

    if ($request_type) {
        try {
            $data = array();
            switch ($request_type) {
                case 'create': // CREATE NEW PAYMENT
                    // get & verify filled-in dataa
                    $data = get_payment_form_data();
                    if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
                        $_SESSION['data'] = $data;
                        session_result('error', 'Akciu sa nepodarilo vykonať. 01');
                        break;
                    }
                    // DB INSERT
                    $mysqli->begin_transaction();
                    $query = "INSERT INTO payment SET create_datetime=NOW(), author_id=" . $_SESSION['user_id'];
                    foreach ($data AS $key => $value) $query .= ", $key='$value'";
                    if ($mysqli->query($query)) {
                        // success
                        $mysqli->commit();
                        session_result('success', 'Platba bola vytvorená.');
                    } else {
                        // fail
                        $mysqli->rollback();
                        session_result('success', 'Platba nebola vytvorená.');
                    }
                    break;
                case 'cancel': // CANCEL PAYMENT MANIPULATION
                    session_result('warning', 'Platba nebola upravená.');
                    header("Location: payment-overview.php?pid=" . $payment_id);
                    exit();
                case 'modify': // MODIFY PAYMENT
                    $data = get_payment_form_data();
                    if ((isset($_SESSION['error']) && !empty($_SESSION['error'])) || !$payment_id) {
                        session_result('error', 'Akciu sa nepodarilo vykonať. 02 pid= ' . $payment_id);
                        break;
                    }
                    $mysqli->begin_transaction();
                    $query = "UPDATE payment SET author_id=" . $_SESSION['user_id'];
                    foreach ($data AS $key => $value) {
                        if ($value == '') {
                            $query .= ", $key=NULL"; // empty string -> NULL in DB
                        } else {
                            $query .= ", $key='$value'";
                        }

                    }
                    $query .= ' WHERE id=' . $payment_id ;
                    if (!$mysqli->query($query)) {
                        $mysqli->rollback();
                        session_result('error', 'Akciu sa nepodarilo vykonať. 03');
                        break;
                    }
                    $mysqli->commit();
                    session_result('success', 'Platba bola upravená.');
                    break;
                case 'delete':

                    $mysqli->begin_transaction();
                    $query = "DELETE FROM payment WHERE id=$payment_id";
                    if (!$mysqli->query($query)) {
                        $mysqli->rollback();
                        session_result('error', 'Akciu sa nepodarilo vykonať. 04 query= ' . $query . '<br>' . $mysqli->error);
                        break;
                    }
                    $mysqli->commit();
                    session_result('success', 'Platba bola odstránená.');
                    break;
            }
        } catch (mysqli_sql_exception $exception) {
            $mysqli->rollback();
            session_result('error', 'Akciu sa nepodarilo vykonať. 05 ' . $exception);
        } finally {
            $_SESSION['data'] = $data;
            $_SESSION['result_message'] .= $mysqli->error;
            header("Location: payment-modify.php");
            exit();
        }
    }

    // show result info
    session_result_echo();

    // show errors
    if (isset($_SESSION['error'])) {
        foreach ($_SESSION['error'] AS $value) {
            echo '<p class="error">' . $value . '</p>' ;
        }
        unset($_SESSION['error']);

        get_payment_form($mysqli, $_SESSION['data'], $request_type);
        unset($_SESSION['data']);
    } else {
        // show form
        get_payment_form($mysqli, false, $request_type);
    }


}

function get_payment_form_data() {
    // get & verify data
    $data = array();
    $error = array();

    if (isset($_POST['item_id'])) {
        $data['item_id'] = post_escaped('item_id');
        if ($data['item_id'] < 1) $error['item_id'] = 'Prosím zvoľte položku.';
    }

    if (isset($_POST['client_id'])) {
        $data['client_id'] =  intval(post_escaped('client_id'));
        if ($data['client_id'] < 1) $error['client_id'] = 'Prosím zvoľte platcu.';
    }

    if (isset($_POST['amount'])) {
        $data['amount'] =  intval(post_escaped('amount'));
        if ($data['amount'] < 1) $error['client_id'] = 'Prosím zvoľte počet.';
    }

    if (isset($_POST['pay_datetime_switch'])) {
        if (post_escaped('pay_datetime_switch') == 'wait') {
            // awaiting payment -> date should be set
            if (isset($_POST['due_datetime'])) {
                $data['due_datetime'] =  post_escaped('due_datetime');
                if ($data['due_datetime'] == '') {
                    unset($data['due_datetime']);
                    $error['due_datetime'] = "Prosím vyberte čas splatnosti.";
                }
                $data['pay_datetime'] = ''; // payment waiting -> time of payment:=NULL
            } else {
                $error['due_datetime'] = "Prosím vyberte čas splatnosti.";
            }
        } else {
            // payment done -> date and method should be set
            if (isset($_POST['pay_datetime'])) {
                $data['pay_datetime'] =  post_escaped('pay_datetime');
                if ($data['pay_datetime'] == '') {
                    unset($data['pay_datetime']);
                    $error['pay_datetime'] = "Prosím vyberte čas registrácie platby.";
                }
            } else {
                $error['pay_datetime'] = "Prosím vyberte čas registrácie platby.";
            }

            $data['type'] = post_escaped('type');
        }
    } else {
        $error['payment_wait_switch'] = 'Prosím vyberte stav platby.';
    }

    if (!empty($error)) {
        $data['pay_datetime_switch'] = post_escaped('pay_datetime_switch');
        $data['payment_id'] = post_escaped('payment_id');
    }

    // return data
    $_SESSION['error'] = $error;
    return $data;
}


function get_payment_form($mysqli, $form_data = false, $type = 'create') {
    // get item id
    $request_type = 'create';
    if (isset($_POST['request_type'])) $request_type = post_escaped('request_type');
    if (!$form_data) {
        if (isset($_POST['payment_id'])) {
            if ($item = get_payment($mysqli, post_escaped('payment_id'))) {
                // display form for item modification
                $form_data = $item;
                $form_data['payment_id'] = intval(post_escaped('payment_id'));

                // decide if payment is done or waiting
                $form_data['pay_datetime_switch'] = 'wait';
                if (isset($form_data['pay_datetime'])) $form_data['pay_datetime_switch'] = 'done';


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
            <?php //if ($request_type == 'modify') { ?>
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
            if ($selected && $selected == $row['id']) $output .= 'selected';
            $output .= '>' . $row['name'] . '</option>';
            echo $output;
        }
        $result->free();
    }
}

function get_payment($mysqli, $item_id = 0) {
    $query  = " SELECT * FROM payment WHERE id = $item_id";
    $result = db_query($mysqli, $query);
    $return_val = false;
    if (!is_null($result) && $result->num_rows > 0) {
        if ($row = $result->fetch_assoc()) {
            $return_val = $row;
        }
        $result->free();
    }
    return $return_val;
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
