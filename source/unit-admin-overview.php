
<?php
    date_default_timezone_set("Europe/Bratislava");
    session_start();
    include('db.php');              /* @var mysqli $mysqli */
    include('functions.php');       // basic functions
    include('login-verify.php');    // login/logout
    header_include();
    require_user_logged_in();
    require_user_level('lector');
    nav_include();
    include('unit-admin-functions.php');
?>

<script src="unit-admin-functions.js"></script>

<section class="full-width">
    <h1>Skupiny a udalosti</h1>
    <p class="info">
        Tu môžete spravovať dostupné skupiny a udalosti. Kliknite na skupinu alebo udalosť pre zobrazenie a úpravu detailov.<br>
        <strong>Skupiny</strong> sú množinami klientov a (alebo) udasostí a slúžia pre hromadnú manipuláciu s nimi.<br>
        <strong>Udalosti</strong> sú buď to jedorazové alebo s viacnásobným výskytom, môžu byť súčasťou skupiny.
    </p>

    <h2>Skupiny</h2>
    <button class="button-create-new" type="button" onclick="create_unit('course')">Nová skupina</button>
    <div class="table-container">
        <table>
            <tr>
                <th>Názov</th>
                <th>Obsadenosť</th>
                <th>Registrácia</th>
                <th>Možnosti</th>
            </tr>
            <?php get_units_of_lector($mysqli, 'course'); ?>
        </table>
    </div>

    <h2>Udalosti</h2>
    <button class="button-create-new" type="button" onclick="create_unit('singleevent')">Nová jednorazová udalosť</button>
    <button class="button-create-new" type="button" onclick="create_unit('event')">Nová udalosť s výskytmi</button>
    <div class="table-container">
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
    </div>

</section>

<?php
    include('footer.php');
?>
