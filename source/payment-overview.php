
<?php

    /*
     * Page for accountant - payment lest overview.
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

    $unit_id = get_escaped('unitID', false, true);
    $client_id = get_escaped('clientID', false, true);
?>

<section class="full-width">
    <h1>Platby</h1>
    <?php if (!$unit_id && !$client_id) { ?>
    <p class="info">
        Tu sú zobrazené všetky vytvorené platby. Predstavujú spojenie medzi položkou a klientom.<br>
        Podľa parametrov môže byť platba v stave "neuhradená" alebo "uhradená", v oboch prípadoch i "po splatnosti".
    </p>
    <?php } ?>
    <?php session_result_echo(); ?>
    <?php echo_unit_info_header($mysqli, $unit_id, $client_id); ?>
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
            <?php get_all_payments($mysqli, $unit_id, $client_id); ?>
        </table>
    </div>


</section>

<?php
    include('footer.php');
?>
