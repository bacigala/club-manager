
<?php

    /*
     * Page for accountant - all payment overview.
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
    include('payment-functions.php');
?>

<section class="full-width">
    <h1>Platby</h1>
    <p class="info">
        Tu sú zobrazené všetky vytvorené platby. Predstavujú spojenie medzi položkou a klientom.<br>
        Podľa parametrov môže byť platba v stave "neuhradená" alebo "uhradená", v oboch prípadoch i "po splatnosti".
    </p>
    <?php session_result_echo(); ?>
    <button class="button-create-new" onclick="window.location.href = 'payment-modify.php';">Nová platba</button>
    <div class="table-container">
        <table>
            <tr>
                <th>Vytvorené</th>
                <th>Klient</th>
                <th>Položka</th>
                <th>Množstvo</th>
                <th>Suma</th>
                <th>Platba</th>
                <th>Možnosti</th>
            </tr>
            <?php get_all_payments($mysqli); ?>
        </table>
    </div>
</section>

<?php
    include('footer.php');
?>
