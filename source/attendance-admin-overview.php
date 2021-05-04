
<?php
	date_default_timezone_set("Europe/Bratislava");
	session_start();
	include('functions.php');
	include('db.php');
    include('login-verify.php'); // login check
	header_include();

	if (isset($_SESSION['has_user']) && $_SESSION['has_user']) {
		// user logged-in
        nav_include();

        // user needs to be tutor
        if (!isset($_SESSION['user_is_tutor']) || !$_SESSION['user_is_tutor']) {
            echo 'Na prístup k tejto stránke nemáte oprávnenie.';
            die();
        }

        include('attendance-admin-functions.php');

?>

<script src="attendance-admin-functions.js"></script>

<section class="full-width">
  <h1>Dochádzka</h1>
    <?php get_unit_detail($mysqli); ?>

	<h2>Záznamy</h2>
    <div class="table-container">
        <table>
            <tr>
                <th>Názov</th>
                <th>Dátum a čas</th>
                <th>Stav</th>
            </tr>
            <?php unit_get_attendance($mysqli); ?>
        </table>
    </div>
	
</section>

<?php
		include('footer.php');
	} else {
		// user NOT logged-in
		include('login-form.php');
	}	
?>