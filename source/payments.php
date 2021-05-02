
<?php
    date_default_timezone_set("Europe/Bratislava");
    session_start();
    include('db.php');              /* @var mysqli $mysqli */
    include('functions.php');       // basic functions
    include('login-verify.php');    // login/logout
    header_include();
    require_user_logged_in();
    nav_include();
?>

<section>
    <h1>Platby</h1>
    <div class="table-container">
        <table>
            <tr>
                <th>Položka</th>
                <th>Dátum zaúčtovania</th>
                <th>Množstvo</th>
                <th>Suma</th>
                <th>Dátum platby</th>
            </tr>
            <?php get_payment_list($mysqli); ?>
        </table>
    </div>
</section>

<?php
    include('payments-aside.php');
    include('footer.php');
?>
