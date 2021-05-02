
<?php
	date_default_timezone_set("Europe/Bratislava");
	session_start();
	include('db.php');              /* @var mysqli $mysqli */
    include('functions.php');       // basic functions
    include('login-verify.php');    // login/logout
	header_include();
	require_user_logged_in();
    require_user_level('accountant');
    nav_include();
    include('item-functions.php');
?>

<section class="full-width">
    <h1>Položky</h1>
    <p class="info">
        Tu sú zobrazené všetky vytvorené položky.
        Predstavujú zakúpiteľný "predmet", ktorý vytvorením platby možno účtovať klientom.<br>
        Položku možno asociovať so skupinou alebo udalosťou na určitý čas.
    </p>
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
?>
