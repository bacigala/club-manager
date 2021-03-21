<?php
	date_default_timezone_set("Europe/Bratislava");
	include('functions.php');	
	header_include('Club manager', 'full-width');
?>

<section id="login-section">
	<form method="post" id="login-form">
		<label for="username">Používateľské meno</label><br>
		<input name="username" type="text" id="username" value="" size="20" maxlength="20"><br>
		<label for="password">Heslo</label><br>
		<input name="password" type="password" id="password" size="20" maxlength="20"><br>
		<input name="submit" type="submit" id="submit" value="Prihlás">
	</form>
</section>

<?php
	include('footer.php');
?>