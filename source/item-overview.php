
<?php
	date_default_timezone_set("Europe/Bratislava");
	session_start();
	include('functions.php');
	include('db.php'); /* @var mysqli $mysqli */
    include('login-verify.php'); // login check
	header_include();

	if (isset($_SESSION['has_user']) && $_SESSION['has_user']) {
		// user logged-in
        nav_include(true);
        require_user_level('accountant');
        include('item-functions.php');
?>

<section class="full-width-section">
    <h1>Položky - prehľad</h1>
	<div id="sectionh1negativemarginfix"></div>
    <p class="info">Tu sú zobrazené všetky vytvorené položky, ktoré možno účtovať klientom. Môžu byť asociované s SUV.</p>
    <?php session_result_echo(); ?>
    <button class="button-create-new" onclick="window.location.href = 'item-modify.php';">Nová položka</button>
    <div class="table-container">
        <table>
            <tr>
                <th>Názov</th>
                <th>Suma</th>
                <th>Asociovaná udalosť</th>
                <th>Od</th>
                <th>Do</th>
                <th>Možnosti</th>
            </tr>
            <?php get_item_list($mysqli, intval(get_escaped('highlight'))); ?>
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