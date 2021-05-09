<?php

session_start();
include('../functions.php');
require_user_logged_in();
include('../db.php'); /* @var mysqli $mysqli */
include_once('../payment-client-functions.php');

// get parameters
$task = post_escaped('task');

switch ($task) {
    case 'create':
        create_payment($mysqli);
        break;
    case 'delete':
        delete_payment($mysqli);
        break;
    case 'credit':
        pay_by_credit($mysqli);
        break;
}

// create payment for logged-in user
function create_payment($mysqli) {
    $item_id = intval(post_escaped('iid'));
    $amount = floatval(post_escaped('amount'));

    if (!is_float($amount) || !is_integer($item_id)) {
        echo "AJAX: Invalid parameters received.";
        echo "\n" . $item_id . "\n" . $amount;
        die();
    }

    try {
        $mysqli->begin_transaction();

        // CREATE TRANSACTION
        $query = "INSERT INTO payment SET client_id='{$_SESSION['user_id']}', item_id='$item_id', amount='$amount', author_id='1', create_datetime=NOW()";
        if (!$mysqli->query($query))
            throw new mysqli_sql_exception("Nebolo možné vytvoriť záznam. (DB ERROR 01)");

        $mysqli->commit();
    } catch (mysqli_sql_exception $exception) {
        $mysqli->rollback();
        echo $mysqli->error . "\n" . $exception->getMessage();
    }
}

// delete payment (of not-yet-paid-credit)
function delete_payment($mysqli) {
    $payment_id = post_escaped('pid');

    // delete transaction only if it belongs to logged-in user and has not been payed yet
    $query = "DELETE FROM payment"
            . " WHERE client_id='{$_SESSION['user_id']}' AND id='$payment_id' AND pay_datetime IS NULL AND item_id='0' AND transaction_id IS NULL";
    if (!$mysqli->query($query))
        echo "Platbu nebolo možné odstrániť.";
    echo $mysqli->error;
}

// pay payment by credit
function pay_by_credit($mysqli) {
    $payment_id = post_escaped('pid');

    try {
        $mysqli->begin_transaction();

        $credit_balance = get_credit_balance($mysqli);

        // pay by credit
        $query = "UPDATE payment SET pay_datetime=NOW(), type='credit' "
                . " WHERE client_id='{$_SESSION['user_id']}' AND id='$payment_id' AND  (amount * unit_price) <= '$credit_balance' AND pay_datetime IS NULL and type IS NULL";
        if (!$mysqli->query($query))
            throw new mysqli_sql_exception("Nebolo možné vytvoriť záznam. (DB ERROR 01)");

        $mysqli->commit();
    } catch (mysqli_sql_exception $exception) {
        $mysqli->rollback();
        echo $mysqli->error . "\n" . $exception->getMessage();
    }
}