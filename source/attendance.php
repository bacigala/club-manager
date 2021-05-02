

<?php
    date_default_timezone_set("Europe/Bratislava");
    session_start();
    include('db.php');              /* @var mysqli $mysqli */
    include('functions.php');       // basic functions
    include('login-verify.php');    // login/logout
    header_include();
    require_user_logged_in(true);
    nav_include();
?>

<section>
    <h1>Dochádzka</h1>

    <h2>Záznamy</h2>
    <div class="table-container">
        <table>
            <tr>
                <th>Názov</th>
                <th>Dátum a čas</th>
                <th>Stav</th>
            </tr>
            <?php get_attendance_of_client($mysqli); ?>
        </table>
    </div>
</section>

<?php
    include('attendance-aside.php');
    include('footer.php');
?>
