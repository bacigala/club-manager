
<?php

    /*
     * Page for accountant - all transactions overview.
     */

    date_default_timezone_set("Europe/Bratislava");
    session_start();
    include('db.php');              /* @var mysqli $mysqli */
    include('functions.php');       // basic functions
    include('login-verify.php');    // login/logout
    header_include();
    require_user_logged_in();
    require_user_level('accountant');
    nav_include();
    include('transaction-functions.php');
?>

<script src="transaction-functions.js"></script>

<section class="full-width">
    <h1>Transakcie</h1>
    <p class="info">
        Tu sú zobrazené všetky vytvorené transakcie. Predstavujú spojenie viacerých platieb do skupiny, ktorej sú priradené platobné detaily.<br>
        Transakcie vytvárajú klienti.
    </p>
    <?php session_result_echo(); ?>
    <div class="table-container">
        <table>
            <tr>
                <th>Vytvorené</th>
                <th>Klient</th>
                <th>Názov</th>
                <th>VS</th>
                <th>Suma</th>
                <th>Platba</th>
            </tr>
            <?php get_all_transactions($mysqli); ?>
        </table>
    </div>
</section>

<?php
    include('footer.php');
?>
