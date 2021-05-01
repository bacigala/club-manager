<?php
	date_default_timezone_set("Europe/Bratislava");
	session_start();
	include('functions.php');
	include('db.php');
	include('login-verify.php'); // login check
	header_include();

	if (isset($_SESSION['has_user']) && $_SESSION['has_user']) {
		// user logged-in
        nav_include();
?>

<section>
    <h1>Vitajte <?php echo($_SESSION['user_name']); ?>!</h1>
		<div id="sectionh1negativemarginfix"></div>

        <?php if ($_SESSION['user_is_client']) {  // CLIENT ?>
            <h2>Možnosti</h2>
            <a class="index-function-option" href="unit-overview.php">Prehľad udalostí & prihlasovanie</a>
            <a class="index-function-option" href="attendance.php">Dochádzka</a>
            <a class="index-function-option" href="payments.php">Poplatky</a>
        <?php } else { ?>

            <?php if ($_SESSION['user_is_accountant']) { // ACCOUNTANT ?>
                <h2>Financie</h2>
                <a class="index-function-option" href="item-overview.php">Prehľad položiek</a>
                <a class="index-function-option" href="payment-overview.php">Prehľad platieb</a>
            <?php } ?>

            <?php if ($_SESSION['user_is_tutor']) {  // TUTOR (LECTOR) ?>
                <h2>Správa skupín a udalostí</h2>
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
	} else {
		// user NOT logged-in
		include('login-form.php');
	}	
?>