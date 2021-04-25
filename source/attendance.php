
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
  <h1>Dochádzka</h1>
	<div id="sectionh1negativemarginfix"></div>
	
	<h2>Záznamy</h2>
	<table>
		<tr>
			<th>Názov</th>
			<th>Dátum a čas</th>
			<th>Stav</th>
		</tr>
		<?php get_attendance_of_client($mysqli); ?>
	</table>
	
</section>

<?php
		include('attendance-aside.php');
		include('footer.php');
	} else {
		// user NOT logged-in
		include('login-form.php');
	}	
?>