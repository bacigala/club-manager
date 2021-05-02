
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
    include('item-functions.php');
?>

<section class="full-width">
    <h1>Položka</h1>
    <?php session_result_echo(); ?>
    <?php handle_item_modify($mysqli); ?>
</section>

<?php
    include('footer.php');
?>
