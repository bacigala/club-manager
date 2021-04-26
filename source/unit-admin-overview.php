
<script src="unit-admin-functions.js"></script>

<?php
	date_default_timezone_set("Europe/Bratislava");
	session_start();
	include('functions.php');
	include('db.php');
    include('login-verify.php'); // login/logout request
	header_include();

	if (isset($_SESSION['has_user']) && $_SESSION['has_user']) {
		// user logged-in
		nav_include();
		include('unit-admin-functions.php');
        ?>

        <section>
            <h1>Skupiny a udalosti</h1>
            <div id="sectionh1negativemarginfix"></div>

            <h2>Skupiny</h2>
            <table>
                <tr>
                    <th>Názov</th>
                    <th>Obsadenosť</th>
                    <th>Registrácia</th>
                    <th>Možnosti</th>
                </tr>
                <?php get_units_of_lector($mysqli, 'course'); ?>
            </table>

            <h2>Udalosti</h2>
            <table>
                <tr>
                    <th>Názov</th>
                    <th>Typ</th>
                    <th>Obsadenosť</th>
                    <th>Stav</th>
                    <th>Možnosti</th>
                </tr>
                <?php get_units_of_lector($mysqli, 'event'); ?>
            </table>

        </section>

        <?php
		include('unit-admin-aside.php');
		include('footer.php');
	} else {
		// user NOT logged-in
		include('login-form.php');
	}	
?>