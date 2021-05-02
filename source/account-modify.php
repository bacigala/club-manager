
<?php
    date_default_timezone_set("Europe/Bratislava");
    session_start();
    include('db.php');              /* @var mysqli $mysqli */
    include('functions.php');       // basic functions
    include('login-verify.php');    // login/logout
    header_include();
    require_user_logged_in();
    require_user_level('admin');
    nav_include();
    include('account-admin-functions.php');
?>

<section class="full-width">
    <h1>Zamestanec</h1>
    <?php handle_account_modify($mysqli); ?>
</section>

<?php
    include('footer.php');
?>
