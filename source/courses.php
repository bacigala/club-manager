<?php
	date_default_timezone_set("Europe/Bratislava");
	include('functions.php');	
	header_include('Club manager');
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
	include('aside.php');
	include('footer.php');
?>