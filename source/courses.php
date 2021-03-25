
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
  <h1>Courses</h1>
	
	<table>
		<tr>
			<th>Name</th>
			<th>Status</th>
		</tr>
		<tr>
			<td>Course A</td>
			<td>Open</td>
		</tr>
		<tr>
			<td>Course B</td>
			<td>Closed</td>
		</tr>
	</table>
</section>

<?php
		include('index-aside.php');
		include('footer.php');
	} else {
		// user NOT logged-in
		include('login-form.php');
	}	
?>