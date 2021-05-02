
<?php
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

<script src="payment-functions.js"></script>

<section class="full-width">
    <h1>Platba</h1>
    <?php session_result_echo(); ?>
    <?php handle_payment_modify($mysqli); ?>
</section>

<?php
	include('footer.php');
?>
