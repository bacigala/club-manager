
<section id="login-section" class="full-width">
	<form method="post" id="login-form">
        <?php if (!user_logged_in()) {session_result_echo(); session_destroy();} ?>
		<label for="username">Používateľské meno</label><br>
		<input name="username" type="text" id="username" value="" size="20" maxlength="20" autofocus><br>
		<label for="password">Heslo</label><br>
		<input name="password" type="password" id="password" size="20" maxlength="20"><br>
		<input name="submit" type="submit" id="submit" value="Prihlás"><br>
	</form>
    <p class="clearfix"></p>
    <a id="new-registration" href="client-modify.php">Registrácia</a>
</section>
