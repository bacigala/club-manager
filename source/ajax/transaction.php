<?php

session_start();
include('../functions.php');
require_user_logged_in();
include('../db.php'); /* @var mysqli $mysqli */

// get parameters
$task = post_escaped('task');

switch ($task) {
    case 'create':
        create_transaction($mysqli);
        break;
    case 'delete':
        delete_transaction($mysqli);
        break;
    case 'setPayDate':
        transaction_set_pay_date($mysqli);
        break;
}


function create_transaction($mysqli) {
    $payment_list = post_escaped('payments');
    $payment_ids = explode(",", $payment_list);

    try {
        $mysqli->begin_transaction();

        // GET NAME FOR NEW TRANSACTION (next int for user)
        $transaction_name = '0';
        $query = "SELECT max(name) AS 'name_max' FROM transaction WHERE client_id='{$_SESSION['user_id']}'";
        if (!($result = $mysqli->query($query)))
            throw new mysqli_sql_exception("Nebolo mozne vytvorit transakciu. (DB ERROR 01)");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $transaction_name = $row['name_max'];
        } else
            throw new mysqli_sql_exception("DB ERROR 04");
        $transaction_name++;

        // CREATE TRANSACTION
        $query = "INSERT INTO transaction SET name='$transaction_name', client_id='{$_SESSION['user_id']}', variable_symbol='0000'";
        if (!$mysqli->query($query))
            throw new mysqli_sql_exception("Nebolo mozne vytvorit transakciu. (DB ERROR 02)");
        $transaction_id = $mysqli->insert_id;

        // associate payments with new transaction
        // + check: all payments belong to logged-in user and are not yet payed or associated with transaction
        foreach ($payment_ids as $payment_id) {
            $query = "UPDATE payment SET transaction_id='$transaction_id' "
                . " WHERE id='$payment_id' AND transaction_id IS NULL AND client_id='{$_SESSION['user_id']}' ";
            if (!($result = $mysqli->query($query)))
                throw new mysqli_sql_exception("Unable to associate payment with new transaction. Please tr again. (DB-ERRORPID:" . $payment_id . ")");
        }

        // DETERMINE TRANSACTION AMOUNT
        $transaction_price = 0;
        $query = "SELECT sum(amount * unit_price) AS 'transaction_price' FROM payment WHERE transaction_id='$transaction_id'";
        if (!($result = $mysqli->query($query)))
            throw new mysqli_sql_exception("Nebolo mozne vytvorit transakciu. (DB ERROR 03)");
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $transaction_price = $row['transaction_price'];
        } else
            throw new mysqli_sql_exception("DB ERROR 05");

        // DETERMINE TRANSACTION VARIABLE_SYMBOL
        $variable_symbol = '1' . sprintf("%04d", $_SESSION['user_id']) . sprintf("%04d", $transaction_name);

        // UPDATE TRANSACTION TO SET PRICE AND VARIABLE_SYMBOL
        $query = "UPDATE transaction SET price='$transaction_price', variable_symbol='$variable_symbol'"
            . " WHERE id='$transaction_id'";
        if (!$mysqli->query($query))
            throw new mysqli_sql_exception("Nebolo mozne vytvorit transakciu. (DB ERROR 06)");

        $mysqli->commit();
    } catch (mysqli_sql_exception $exception) {
        $mysqli->rollback();
        echo $mysqli->error . '\n' . $exception->getMessage();
    }
}

function delete_transaction($mysqli) {
    $transaction_id = post_escaped('tid');

    // delete transaction only if it belongs to logged-in user and has not been payed yet
    $query = "DELETE FROM transaction"
            . " WHERE client_id='{$_SESSION['user_id']}' AND id='$transaction_id' AND datetime_pay IS NULL";
    if (!$mysqli->query($query))
        echo "Transakciu nebolo možné odstrániť.";
}

function transaction_set_pay_date($mysqli) {
    $transaction_id = post_escaped('tid');
    $pay_datetime = post_escaped("payDatetime");

    $status = 'fail';

    if ($pay_datetime == 'NULL') $query = "UPDATE transaction SET datetime_pay=NULL WHERE id=$transaction_id";
    else $query = "UPDATE transaction SET datetime_pay='$pay_datetime' WHERE id=$transaction_id";
    if ($mysqli->query($query)) {
        $status = 'OK';
    }

    $dom = new DOMDocument();
    $dom->encoding = 'utf-8';
    $dom->xmlVersion = '1.0';
    $root = $dom->createElement('DBtransaction');
    $child_node_result_status = $dom->createElement('status', $status);
    $root->appendChild($child_node_result_status);
    $child_node_result_message = $dom->createElement('message', 'Oukej');
    $root->appendChild($child_node_result_message);
    $child_node_new_value = $dom->createElement('value', $pay_datetime);
    $root->appendChild($child_node_new_value);
    $dom->appendChild($root);

    echo $dom->saveXml();
}