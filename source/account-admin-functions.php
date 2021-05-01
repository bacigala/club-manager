<?php

/**
 * Output <tr>s for account-list.
 * @param $mysqli
 */
function get_account_list($mysqli) {
    $query = "SELECT username, id, name, surname, last_logon, is_accountant, is_tutor, is_admin FROM account WHERE id > 1 ORDER BY surname";

    $result = db_query($mysqli, $query);
    if (!is_null($result) && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $output  = '<tr>';
            $output .= '<td>' . $row['username'] . '</td>';
            $output .= '<td>' . $row['name'] . '</td>';
            $output .= '<td>' . $row['surname'] . '</td>';
            $output .= '<td>' . $row['last_logon'] . '</td>';

            $output .= '<td class="desktop-only-block">';
            $output .= '<input type="checkbox" data-account_id="' . $row['id'] . '" id="is_tutor_' . $row['id'] . '" name="is_tutor"' . ($row['is_tutor'] ? 'checked' : '') . ' onclick="update_privilege(this, ' . $row['id'] . ', \'is_tutor\')">';
            $output .= '<label for="is_tutor_' . $row['id'] . '">Lektor</label>';
            $output .= '<input type="checkbox" data-account_id="' . $row['id'] . '" id="is_accountant_' . $row['id'] . '" name="is_accountant" value="true" ' . ($row['is_accountant'] ? 'checked' : '') . ' onclick="update_privilege(this, ' . $row['id'] . ', \'is_accountant\')">';
            $output .= '<label for="is_accountant_' . $row['id'] . '">Účtovník</label>';
            $output .= '<input type="checkbox" data-account_id="' . $row['id'] . '" id="is_admin_' . $row['id'] . '" name="is_admin" value="true" ' . ($row['is_admin'] ? 'checked' : '') . ' onclick="update_privilege(this, ' . $row['id'] . ', \'is_admin\')">';
            $output .= '<label for="is_admin_' . $row['id'] . '">Administrátor</label>';
            $output .= '</td>';

            $output .= '<td><form method="post" action="account-modify.php" class="table-form">';
            $output .= '<input type="hidden" name="account_id" value="' . $row['id'] . '" />';
            $output .= '<input type="submit" class="main-form-option-button" name="modify_request" value="Upraviť"/>';
            $output .= '</form></td>';

            $output	.= '</tr>';
            echo $output;
        }
        $result->free();
    } else {
        echo '<tr><td colspan="5">the_account_list</td></tr>';
    }
}


/**
 * Fetch one accout record from DB.
 * @param mysqli $mysqli
 * @param int $account_id
 * @return array|string DB row OR empty string on error
 */
function get_account($mysqli, $account_id = 0) {
    $return_value = '';
    $query = "SELECT id, username, name, surname, is_tutor, is_accountant, is_admin, email FROM account WHERE id=?";
    if ($statement = $mysqli->prepare($query)) {
        $statement->bind_param("i", $account_id);
        $statement->execute();
        $result = $statement->get_result();
        if ($result->num_rows > 0)
            return $result->fetch_assoc();
    }
    return $return_value;
}


/**
 * Echo form for account medification.
 * @param mysqli $mysqli
 */
function get_account_form($mysqli) {
    $form_data = false;

    if (isset($_POST['account_id'])) {
        if ($account = get_account($mysqli, post_escaped('account_id'))) {
            // display form for item modification
            $form_data = $account;
        } else {
            // requested item does not exist
            echo "<p class='error'>Requested account does not exist.</p>";
            return;
        }
    } else if (isset($_SESSION['data'])) {
        // form re-fill after not being submitted (errors)
        $form_data = $_SESSION['data'];
    }

    ?>
    <form method="post" class="master-form">

        <input type="hidden" name="account_id" value="<?php echo post_escaped('account_id'); ?>"/>

        <fieldset>
            <legend>Osobné údaje</legend>

            <label for="name" class="required">Meno</label>
            <input type="text" name="name" id="name" maxlength="40" value="<?php if ($form_data && isset($form_data['name'])) echo $form_data['name']; ?>">

            <label for="surname" class="required">Priezvisko</label>
            <input type="text" name="surname" id="surname" maxlength="40" value="<?php if ($form_data && isset($form_data['surname'])) echo $form_data['surname']; ?>">

            <label for="email" class="required">Email</label>
            <input type="text" name="email" id="email" maxlength="40" value="<?php if ($form_data && isset($form_data['email'])) echo $form_data['email']; ?>">
        </fieldset>

        <fieldset>
            <legend>Prihlasovacie údaje</legend>

            <label for="username" class="required">Prihlasovacie meno</label>
            <input type="text" name="username" id="username" maxlength="40" value="<?php if ($form_data && isset($form_data['username'])) echo $form_data['username']; ?>">

            <label for="password" class="required">Heslo</label>
            <input type="text" name="password" id="password" maxlength="40" value="<?php if ($form_data && isset($form_data['password'])) echo $form_data['password']; ?>">
        </fieldset>

        <fieldset>
            <legend>Práva</legend>

            <label for="is_tutor" class="required">Lektor</label>
            <input type="checkbox" name="is_tutor" id="is_tutor" maxlength="40" value="1" <?php if ($form_data && isset($form_data['is_tutor']) && $form_data['is_tutor']==1) echo 'checked'; ?>>

            <label for="is_accountant" class="required">Účtovník</label>
            <input type="checkbox" name="is_accountant" id="is_accountant" maxlength="40" value="1" <?php if ($form_data && isset($form_data['is_accountant']) && $form_data['is_accountant'] == 1) echo 'checked'; ?>>

            <label for="is_admin" class="required">Administrátor</label>
            <input type="checkbox" name="is_admin" id="is_admin" maxlength="40" value="1" <?php if ($form_data && isset($form_data['is_admin']) && $form_data['is_admin'] == 1) echo 'checked'; ?>>
        </fieldset>

        <fieldset>
            <legend>Potvrdenie</legend>
            <?php if (!isset($_POST['account_id'])) { ?>
                <button name="create" type="submit">Vytvoriť účet</button>
                <button name="cancel" type="submit">Zrušiť</button>
            <?php } else {?>
            <?php  ?>
                <button name="modify" type="submit">Uložiť zmeny</button>
                <button name="cancel" type="submit">Zrušiť zmeny</button>
                <button name="delete" type="submit">Odstrániť účet</button>
            <?php } ?>
        </fieldset>
    </form>
    <?php
}


/**
 * Verify form data, generate errors.
 * @return array form data
 */
function get_account_form_data() {
    $data = array();
    $error = array();

    //osobne udaje
    if (isset($_POST['name'])) {
        $data['name'] = post_escaped('name');
        if ($data['name'] == '') $error['name'] = 'Prosím zadajte meno.';
    }

    if (isset($_POST['surname'])) {
        $data['surname'] = post_escaped('surname');
        if ($data['surname'] == '') $error['surname'] = 'Prosím zadajte priezvisko.';
    }

    $data['email'] = post_escaped('email');

    // prihlasovacie udaje
    if (isset($_POST['username'])) {
        $data['username'] = post_escaped('username');
        if ($data['username'] == '') $error['username'] = 'Prosím zadajte prihlasovacie meno.';
    }

    if (post_escaped('password') != '') {
        $data['password'] = post_escaped('password');
    }

    // prava
    $data['is_tutor'] = post_escaped('is_tutor', 0);
    $data['is_accountant'] = post_escaped('is_accountant', 0);
    $data['is_admin'] = post_escaped('is_admin', 0);

    // return data
    $_SESSION['error'] = $error;
    return $data;
}


/**
 * Main controller.
 * @param mysqli $mysqli
 */
function handle_account_modify($mysqli) {
    $request_type = '';
    if (isset($_POST['create'])) $request_type = 'create';
    if (isset($_POST['cancel'])) $request_type = 'cancel';
    if (isset($_POST['modify'])) $request_type = 'modify';
    if (isset($_POST['delete'])) $request_type = 'delete';

    $account_id = 0;
    if (isset($_POST['account_id'])) $account_id = intval(post_escaped('account_id'));

    if ($request_type) {
        try {
            switch ($request_type) {
                case 'create':
                    // get data
                    $data = get_account_form_data();
                    if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
                        session_result('error', "Účet nebolo možné vytvoriť.");
                        break;
                    }

                    // query
                    $query = "INSERT INTO account SET author_id=" . $_SESSION['user_id'];
                    foreach ($data AS $key => $value) {
                        if ($key == 'password') {
                            $query .= ", $key=SHA2('$value',256)";
                            continue;
                        }
                        $query .= ", $key='$value'";
                    }
                    if (!$mysqli->query($query)) {
                        session_result('error', "Účet nebolo možné vytvoriť. (DB ERROR)");
                        break;
                    }
                    $account_id = $mysqli->insert_id;
                    session_result('success', "Účet bol vytvorený." );
                    break;
                case 'cancel':
                    session_result('warning', 'Účet nebol vytvorený/upravený.');
                    break;
                case 'modify':
                    // get data
                    $data = get_account_form_data();
                    if ((isset($_SESSION['error']) && !empty($_SESSION['error'])) || !$account_id) {
                        session_result('error', "Účet nebolo možné upraviť.");
                        break;
                    }
                    // query
                    $query = "UPDATE account SET author_id=" . $_SESSION['user_id'];
                    foreach ($data AS $key => $value) {
                        if ($key == 'password') {
                            $query .= ", $key=SHA2('$value',256)";
                            continue;
                        }
                        $query .= ", $key='$value'";
                    }
                    $query .= ' WHERE id=' . $account_id ;
                    if (!$mysqli->query($query)) {
                        session_result('error', "Účet nebolo možné upraviť. (DB ERROR)");
                        break;
                    }

                    session_result('success', 'Účet bol upravený.');
                    break;
                case 'delete':
                    // query
                    $query = "DELETE FROM account WHERE id=" . $account_id ;
                    if (!$mysqli->query($query)) {
                        session_result('error', "Účet nebolo možné odstrániť.");
                        break;
                    }
                    session_result('success', 'Účet bol odstránený.');
                    break;
            }
        } catch (mysqli_sql_exception $exception) {
            session_result('error', 'Akciu sa nepodarilo vykonať. (exception)' . $exception);
            if ($request_type == 'delete') {
                $_SESSION['result_message'] = 'Položku nemožno odstrániť. (Brániť tomu môže napríklad existujúca platba s ňou asociovaná.)';
            }
        } finally {
            if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
                // error in form data -> stay on this page
                if (isset($data)) $_SESSION['data'] = $data;
                header("Location: account-modify.php");
            } else {
                // go to account-overview overview
                unset($_SESSION['data']);
                header("Location: client-overview.php?highlight=$account_id");
            }
            exit();
        }
    }

    session_result_echo();

    // list errors
    if (isset($_SESSION['error'])) {
        foreach ($_SESSION['error'] AS $value) {
            echo '<p class="error">' . $value . '</p>' ;
        }
        unset($_SESSION['error']);
    }

    get_account_form($mysqli);
}
