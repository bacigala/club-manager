<?php

include_once('functions.php');

function get_payment_table_tr($mysqli) {
    $credit = get_credit_balance($mysqli);

    $query  = "SELECT payment.type, payment.id, item.id AS 'itemID', item.name, unit_price, pay_datetime, create_datetime, amount, transaction_id, transaction.name AS 'transaction_name'"
    . " FROM payment JOIN item ON (payment.item_id = item.id)"
    . " LEFT JOIN transaction ON (payment.transaction_id = transaction.id)"
    . " WHERE payment.client_id = " . $_SESSION['user_id']
    . " ORDER BY pay_datetime, transaction_name, create_datetime DESC";
    $result = db_query($mysqli, $query);
    if (!is_null($result) && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
    $output  = '<tr class="transaction transaction-' . $row['transaction_name'] .'">';

        // selection
        $output .= '<td onclick="highlight_transaction(' . $row['transaction_name'] . ')">' . (isset($row['transaction_id']) ? ('Transakcia ' . $row['transaction_name']) : (isset($row['pay_datetime']) ? "" : '<input type="checkbox" class="payment_checkbox" id="' . $row['id'] . '">')) . '</td>';
        // name
        $output .= '<td>' . $row['name'] . '</td>';
        // date of creation
        $output .= '<td>' . date_format(date_create($row['create_datetime']), "d.m.Y H:i") . '</td>';
        // amount
        $output .= '<td>' . $row['amount'] . '</td>';
        // price
        $output .= '<td>' . $row['unit_price'] * $row['amount'] . ' €</td>';
        // date of payment
        $pay_datetime = (isset($row['pay_datetime']) ? date_format(date_create($row['pay_datetime']), "d.m.Y H:i") : 'neuhradené');
        $output .= "<td " . (isset($row['pay_datetime']) ? "" : "class='warn'") . ">" . $pay_datetime . '</td>';

        // PAY METHOD / DELETE OPTION (for not-yet-paid credit) / OPTION TO USE CREDIT
        $output .= '<td>';
        if (isset($row['pay_datetime']))
            $output .= translate_payment_type($row['type']);
        if (!isset($row['pay_datetime']) && $row['itemID'] == '0' && !isset($row['transaction_id']))
            $output .= '<input type="button" value="Zrušiť" class="main-form-option-button" onClick="delete_payment(this, ' . $row['id'] . ');">';
        if (!isset($row['pay_datetime']) && $row['itemID'] != '0' && !isset($row['transaction_id']) && $credit >= ($row['unit_price'] * $row['amount']))
            $output .= '<input type="button" value="Uhradiť kreditom" class="main-form-option-button" onClick="pay_by_credit(this, ' . $row['id'] . ');">';
        $output .= '</td>';


        $output .= '</tr>';
    echo $output;
    }
    $result->free();
    }
}

function get_transaction_table_tr($mysqli) {
    $query  = "SELECT * FROM transaction "
    . " WHERE client_id='{$_SESSION['user_id']}' "
    . " ORDER BY datetime_pay, datetime_create";
    $result = db_query($mysqli, $query);
    if (!is_null($result) && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
    $output  = '<tr class="transaction transaction-' . $row['name'] .'">';

        // name
        $output .= '<td onclick="highlight_transaction(' . $row['name'] . ')">Transakcia ' . $row['name'] . '</td>';
        // create
        $output .= '<td>' . date_format(date_create($row['datetime_create']), "d.m.Y H:i") . '</td>';
        // receive
        $pay_datetime = (isset($row['datetime_pay']) ? date_format(date_create($row['datetime_pay']), "d.m.Y H:i") : 'neuhradené');
        $output .= "<td " . (isset($row['datetime_pay']) ? "" : "class='warn'") . ">" . $pay_datetime . '</td>';
        // price
        $output .= '<td>' . $row['price'] . ' €</td>';
        // VS
        $output .= '<td>' . $row['variable_symbol'] . '</td>';

        // DELETE OPTION
        $output .= '<td>';
        if (!isset($row['datetime_pay']))
            $output .= '<input type="button" value="Zrušiť" class="main-form-option-button" onClick="delete_transaction(this, ' . $row['id'] . ');">';
        $output .= '</td>';

        $output .= '</tr>';
    echo $output;
    }
    $result->free();
    }
}

function get_credit_balance($mysqli) {
    $credit_plus = 0;
    $query  = "SELECT SUM(payment.unit_price * payment.amount) AS 'credit' FROM payment "
             . " WHERE client_id='{$_SESSION['user_id']}' AND item_id='0' AND pay_datetime IS NOT NULL";
    $result = db_query($mysqli, $query);
    if (!is_null($result) && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $credit_plus = $row['credit'];
        }
        $result->free();
    } else {
        echo "Unable to determine balance.";
        return;
    }

    $credit_minus = 0;
    $query  = "SELECT SUM(payment.unit_price * payment.amount) AS 'credit' FROM payment "
        . " WHERE client_id='{$_SESSION['user_id']}' AND type='credit' AND pay_datetime IS NOT NULL";
    $result = db_query($mysqli, $query);
    if (!is_null($result) && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $credit_minus = $row['credit'];
        }
        $result->free();
    } else {
        echo "Unable to determine balance.";
        return;
    }

    return $credit_plus - $credit_minus;
}
