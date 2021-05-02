
<?php
    date_default_timezone_set("Europe/Bratislava");
    session_start();
    include('db.php');              /* @var mysqli $mysqli */
    include('functions.php');       // basic functions
    include('login-verify.php');    // login/logout
    header_include();
    require_user_logged_in();
    require_user_level('admin');
    nav_include();
    include('client-admin-functions.php');
    include('account-admin-functions.php');
?>

<script src="client-admin-functions.js"></script>

<section class="full-width">
    <h1>Používateľsé účty</h1>
    <?php session_result_echo(); ?>

    <h2>Zamestnanci</h2>
    <button class="button-create-new" onclick="window.location.href = 'account-modify.php';">Nový účet</button>
    <div class="table-container">
        <table>
            <tr>
                <th>Login</th>
                <th>Meno</th>
                <th>Priezvisko</th>
                <th>Posledné prihlásenie</th>
                <th class="desktop-only-block">Práva</th>
                <th>Možnosti</th>
            </tr>
            <?php get_account_list($mysqli); ?>
        </table>
    </div>

    <h2>Klienti</h2>
    <button class="button-create-new" onclick="window.location.href = 'client-modify.php';">Nový účet</button>
    <div class="table-container">
        <table>
            <tr>
                <th>Meno</th>
                <th>Priezvisko</th>
                <th>Posledné prihlásenie</th>
                <th>Možnosti</th>
            </tr>
            <?php get_client_list($mysqli); ?>
        </table>
    </div>
</section>

<?php
    include('footer.php');
?>
