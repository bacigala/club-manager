<?php

/*
 * Generate <tr>s for all-transactions overview on transaction-overview.php (for accountant)
 */
function get_all_transactions(mysqli $mysqli) {
    $query  = "SELECT client.name AS 'client_name', client.surname, transaction.id, transaction.name AS 'transaction_name', variable_symbol, datetime_create, datetime_pay, price"
            . " FROM transaction JOIN client ON (transaction.client_id = client.id)"
            . " ORDER BY datetime_create DESC";
    $result = db_query($mysqli, $query);
    if (!is_null($result) && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $output  = '<tr>';
            $output .= '<td>' . date_format(date_create($row['datetime_create']), "d.m.Y H:i") . '</td>';
            $output .= '<td>' . $row['client_name'] . ' ' . $row['surname'] . '</td>';
            $output .= '<td>Transakcia ' . $row['transaction_name'] . '</td>';
            $output .= '<td>' . $row['variable_symbol'] . '</td>';
            $output .= '<td>' . $row['price'] . ' €</td>';

            // options
            $output .= ' <td><form>';
            $output .= '<input ' .  (isset($row['datetime_pay']) ? "" : " class='warn-highlight'") . ' type="datetime-local" id="datetime_pay_' . $row['id'] . '" name="" value="' . (isset($row['datetime_pay']) ? input_date_format($row['datetime_pay']) : '') . '">';
            $output .= '<input type="button" name="" value="Uložiť" class="main-form-option-button" onClick="update_transaction_datetime_pay(this, ' . $row['id'] . ');">';
            $output .= ' </form></td>';

            $output .= '</tr>';
            echo $output;
        }
        $result->free();
    }
}
