
<?php
	date_default_timezone_set("Europe/Bratislava");
	session_start();
	include('functions.php');
	include('db.php');
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
?>

<section class="full-width-section">
  <h1>Používatelía - prehľad</h1>
	<div id="sectionh1negativemarginfix"></div>
	<table>
		<tr>
			<th>Meno</th>
			<th>Priezvisko</th>
			<th>Posledné prihlásenie</th>
			<th>Možnosti</th>
		</tr>
		<?php get_client_list($mysqli); ?>
	</table>
	
</section>

<?php
		include('footer.php');
	} else {
		// user NOT logged-in
		include('login-form.php');
	}	
?>