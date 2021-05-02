
<?php
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
    <h1>Vitajte <?php echo($_SESSION['user_name']); ?>!</h1>

        <?php if ($_SESSION['user_is_client']) {  // CLIENT ?>
            <h2>Možnosti</h2>
            <a class="index-function-option" href="units.php">Prehľad udalostí & prihlasovanie</a>
            <a class="index-function-option" href="attendance.php">Dochádzka</a>
            <a class="index-function-option" href="payments.php">Poplatky</a>
        <?php } else { ?>

            <?php if ($_SESSION['user_is_accountant']) { // ACCOUNTANT ?>
                <h2>Financie</h2>
                <a class="index-function-option" href="item-overview.php">Prehľad položiek</a>
                <a class="index-function-option" href="payment-overview.php">Prehľad platieb</a>
            <?php } ?>

            <?php if ($_SESSION['user_is_tutor']) {  // TUTOR (LECTOR) ?>
                <h2>Skupiny a udalosti</h2>
                <a class="index-function-option" href="unit-admin-overview.php">Prehľad a správa</a>
            <?php } ?>

            <?php if ($_SESSION['user_is_admin']) { // ADMINISTRATOR ?>
                <h2>Používateľské účty</h2>
                <a class="index-function-option" href="client-overview.php">Prehľad a správa</a>
            <?php } ?>
        <?php } ?>
</section>

<?php
    include('index-aside.php');
    include('footer.php');
?>
