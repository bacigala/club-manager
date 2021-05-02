
<?php

    /*
     * Page for client - payment overview.
     */

    date_default_timezone_set("Europe/Bratislava");
    session_start();
    include('db.php');              /* @var mysqli $mysqli */
    include('functions.php');       // basic functions
    include('login-verify.php');    // login/logout
    header_include();
    require_user_logged_in();
    nav_include();
?>

    <section>
        <h1>Platby</h1>
        <div class="table-container">
            <table>
                <tr>
                    <th>Položka</th>
                    <th>Dátum zaúčtovania</th>
                    <th>Množstvo</th>
                    <th>Suma</th>
                    <th>Dátum platby</th>
                </tr>
                <?php get_payment_table_tr($mysqli); ?>
            </table>
        </div>
    </section>

<?php
    include('payments-aside.php');
    include('footer.php');


    /*
     * SUPPORT FUNCTIONS
     */

    function get_payment_table_tr($mysqli) {
        $query  = "SELECT * FROM payment JOIN item ON (payment.item_id = item.id)"
                . " WHERE payment.client_id = " . $_SESSION['user_id']
                . " ORDER BY create_datetime DESC";
        $result = db_query($mysqli, $query);
        if (!is_null($result) && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $output  = '<tr>';

                // name
                $output .= '<td>' . $row['name'] . '</td>';
                // date of creation
                $output .= '<td>' . date_format(date_create($row['create_datetime']), "d.m.Y H:i") . '</td>';
                // amount
                $output .= '<td>' . $row['amount'] . '</td>';
                // price
                $output .= '<td>' . $row['price'] * $row['amount'] . '</td>';
                // date of payment
                $pay_datetime = (isset($row['pay_datetime']) ? date_format(date_create($row['pay_datetime']), "d.m.Y H:i") : 'neuhradené');
                $output .= "<td " . (isset($row['pay_datetime']) ? "" : "class='warn'") . ">" . $pay_datetime . '</td>';

                $output .= '</tr>';
                echo $output;
            }
            $result->free();
        }
    }

?>
