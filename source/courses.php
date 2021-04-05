
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
  <h1>Prihlasovanie</h1>
	<div id="sectionh1negativemarginfix"></div>
	
	<?php if (isset($_POST['unit_id']) || isset($_SESSION['result_message'])) handle_course_request($mysqli); ?>
	
	<h2>Skupiny</h2>
	<table>
		<tr>
			<th>Názov</th>
			<th>Obsadenosť</th>
			<th>Stav</th>
			<th>Možnosti</th>
		</tr>
		<?php get_units_of_client($mysqli, 'course'); ?>
	</table>
	
	<h2>Kurzy</h2>
	<table>
		<tr>
			<th>Názov</th>
			<th>Obsadenosť</th>
			<th>Stav</th>
			<th>Možnosti</th>
		</tr>
		<?php get_units_of_client($mysqli, 'event'); ?>
	</table>	
	
	<h2>Udalosti</h2>
	<table>
		<tr>
			<th>Názov</th>
			<th>Obsadenosť</th>
			<th>Stav</th>
			<th>Možnosti</th>
		</tr>
		<?php get_units_of_client($mysqli, 'occurence'); ?>
	</table>
	
</section>

<?php
		include('courses-aside.php');
		include('footer.php');
	} else {
		// user NOT logged-in
		include('login-form.php');
	}	
?>