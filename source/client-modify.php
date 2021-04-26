
<?php
	date_default_timezone_set("Europe/Bratislava");
	session_start();
	include('functions.php');
	include('db.php');
    include('login-verify.php'); // login check
	header_include();
	include('payment-modify-function.php');

	if (isset($_SESSION['has_user']) && $_SESSION['has_user']) {
		// user logged-in
        nav_include(true);
		
		if (!isset($_SESSION['user_is_client']) || $_SESSION['user_is_client']) {
			echo 'Na prístup k tejto stránke nemáte oprávnenie.';
			die();
		}
		// accountant / tuto / admin logged-in
?>

<section class="full-width-section">
  <h1>Používateľ</h1>
	<div id="sectionh1negativemarginfix"></div>
    <?php handle_client_modify($mysqli); ?>
</section>

<?php
		include('footer.php');
	} else {
		// user NOT logged-in
		include('login-form.php');
	}	
?>