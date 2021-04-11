
<?php
	date_default_timezone_set("Europe/Bratislava");
	session_start();
	include('functions.php');
	include('db.php');
  include('login-verify.php'); // login check
	header_include();

	if (isset($_SESSION['has_user']) && $_SESSION['has_user']) {
		// user logged-in
		include('nav.php');
		
		if (!isset($_SESSION['user_is_accountant']) || !$_SESSION['user_is_accountant']) {
			echo 'Na prístup k tejto stránke nemáte oprávnenie.';
			die();
		}
		// accountant logged-in
?>

<section class="full-width-section">
  <h1>Platby - prehľad</h1>
	<div id="sectionh1negativemarginfix"></div>
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
	
</section>

<?php
		//include('payment-aside.php');
		include('footer.php');
	} else {
		// user NOT logged-in
		include('login-form.php');
	}	
?>