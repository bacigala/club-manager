
<?php
    date_default_timezone_set("Europe/Bratislava");
    session_start();
    include('db.php');              /* @var mysqli $mysqli */
    include('functions.php');       // basic functions
    include('login-verify.php');    // login/logout
    header_include();
    require_user_logged_in();
    nav_include();
    include('units-functions.php');
?>

<section>
    <h1>Prihlasovanie</h1>

    <?php session_result_echo(); ?>
    <?php if (isset($_POST['unit_id'])) handle_course_request($mysqli); ?>

    <h2>Skupiny</h2>
    <div class="table-container">
        <table>
            <tr>
                <th>Názov</th>
                <th>Obsadenosť</th>
                <th>Stav</th>
                <th>Možnosti</th>
            </tr>
            <?php get_units_of_client_tr($mysqli, 'course'); ?>
        </table>
    </div>

    <h2>Kurzy</h2>
    <div class="table-container">
        <table>
            <tr>
                <th>Názov</th>
                <th>Obsadenosť</th>
                <th>Stav</th>
                <th>Možnosti</th>
            </tr>
            <?php get_units_of_client_tr($mysqli, 'event'); ?>
        </table>
    </div>

    <h2>Udalosti</h2>
    <div class="table-container">
        <table>
            <tr>
                <th>Názov</th>
                <th>Obsadenosť</th>
                <th>Stav</th>
                <th>Možnosti</th>
            </tr>
            <?php get_units_of_client_tr($mysqli, 'occurence'); ?>
        </table>
    </div>
</section>

<?php
    include('units-aside.php');
    include('footer.php');
?>
