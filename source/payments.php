
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
?>

<section>
  <h1>Platby</h1>
	<div id="sectionh1negativemarginfix"></div>
	<table>
		<tr>
			<th>Položka</th>
			<th>Dátum zaúčtovania</th>
			<th>Množstvo</th>
			<th>Suma</th>
			<th>Dátum platby</th>
		</tr>
		<?php get_payment_list($mysqli); ?>
	</table>
	
</section>

<?php
		include('payment-aside-client.php');
		include('footer.php');
	} else {
		// user NOT logged-in
		include('login-form.php');
	}	
?>