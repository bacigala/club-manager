
<?php
	date_default_timezone_set("Europe/Bratislava");
	session_start();
	include('functions.php');
	include('db.php'); /* @var mysqli $mysqli*/
    include('login-verify.php'); // login check
	header_include();

	if (isset($_SESSION['has_user']) && $_SESSION['has_user']) {
		// user logged-in
        nav_include(true);
		
		if (!isset($_SESSION['user_is_client']) || $_SESSION['user_is_client']) {
			echo 'Na prístup k tejto stránke nemáte oprávnenie.';
			die();
		}
		// accountant logged-in
        include('client-admin-functions.php');
        include('account-admin-functions.php');
?>

<script src="client-admin-functions.js"></script>

<section class="full-width-section">
  <h1>Používateľsé účty - prehľad</h1>
    <div id="sectionh1negativemarginfix"></div>

    <?php session_result_echo(); ?>

    <?php if (require_user_level('admin', false)) { ?>
    <h2>Zamestnanci</h2>
    <button class="button-create-new" onclick="window.location.href = 'account-modify.php';">Nový účet</button>
    <div class="table-container">
        <table>
            <tr>
                <th>Login</th>
                <th>Meno</th>
                <th>Priezvisko</th>
                <th>Posledné prihlásenie</th>
                <th class="desktop-only-block">Práva</th>
                <th>Možnosti</th>
            </tr>
            <?php get_account_list($mysqli); ?>
        </table>
    </div>
    <?php } ?>

    <h2>Klienti</h2>
    <button class="button-create-new" onclick="window.location.href = 'client-modify.php';">Nový účet</button>
    <div class="table-container">
        <table>
            <tr>
                <th>Meno</th>
                <th>Priezvisko</th>
                <th>Posledné prihlásenie</th>
                <th>Možnosti</th>
            </tr>
            <?php get_client_list($mysqli); ?>
        </table>
    </div>
	
</section>

<?php
		include('footer.php');
	} else {
		// user NOT logged-in
		include('login-form.php');
	}	
?>