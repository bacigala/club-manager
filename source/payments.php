
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
    include('payment-client-functions.php');
?>
    <script src="payment-client-functions.js"></script>
    <section>
        <h1>Platby</h1>
        <h2>Účtované položky</h2>
        <button class="button-create-new" type="button" onclick="select_all()">Označiť všetko</button>
        <button class="button-create-new" type="button" onclick="create_transaction()">Označené - IB transakcia</button>
        <button class="button-create-new" type="button" onclick="group_pay_by_credit()">Označené - uhradiť kreditom</button>

        <div class="table-container">
            <table>
                <tr>
                    <th>Výber</th>
                    <th>Položka</th>
                    <th>Dátum zaúčtovania</th>
                    <th>Množstvo</th>
                    <th>Suma</th>
                    <th>Dátum platby</th>
                    <th></th>
                </tr>
                <?php get_payment_table_tr($mysqli); ?>
            </table>
        </div>
        <h2>Transakcie IB</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>Názov</th>
                    <th>Vytvorená</th>
                    <th>Prijatá</th>
                    <th>Suma</th>
                    <th>VS</th>
                    <th></th>
                </tr>
                <?php get_transaction_table_tr($mysqli); ?>
            </table>
        </div>
    </section>

<?php
    include('payments-aside.php');
    include('footer.php');
?>
