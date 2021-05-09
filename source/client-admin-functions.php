<?php

/**
 * Output <tr>s for client-list.
 * @param $mysqli
 */
function get_client_list($mysqli) {
	$query = "SELECT id, name, surname, last_logon FROM client ORDER BY surname";

	$result = db_query($mysqli, $query);		
	if (!is_null($result) && $result->num_rows > 0) {
		while ($row = $result->fetch_assoc()) {			
			$output  = '<tr>';
			$output .= '<td>' . $row['name'] . '</td>';
			$output .= '<td>' . $row['surname'] . '</td>';
			$output .= '<td>' . $row['last_logon'] . '</td>';
			
			$output .= '<td><form method="post" class="table-form" action="client-modify.php" style="display: inline">';
			$output .= '<input type="hidden" name="client_id" value="' . $row['id'] . '" />';
			$output .= '<input type="submit" class="main-form-option-button" name="modify_request" value="Upraviť"/>';
			$output .= '</form>';

            // OPTION: view payments
            $output .= '<button class="main-form-option-button" onclick="event.stopPropagation(); window.location.href = \'payment-overview.php?clientID=' . $row['id'] . '\';">Platby</button>';

            // OPTION: view transactions
            $output .= '<button class="main-form-option-button" onclick="event.stopPropagation(); window.location.href = \'transaction-overview.php?clientID=' . $row['id'] . '\';">Transakcie</button>';


            $output	.= '</td></tr>';
			echo $output;
		}
		$result->free();
	} else {
	    echo "<tr><td colspan='4'>Bez používatelov.</td></tr>";
    }
}


/**
 * Fetch one client record from DB.
 * @param mysqli $mysqli
 * @param int $client_id
 * @return array|string DB row OR empty string on error
 */
function get_client($mysqli, $client_id = 0) {
    $return_value = '';
    $query = "SELECT id, username, name, surname, email FROM client WHERE id=?";
    if ($statement = $mysqli->prepare($query)) {
        $statement->bind_param("i", $client_id);
        $statement->execute();
        $result = $statement->get_result();
        if ($result->num_rows > 0)
            return $result->fetch_assoc();
    }
    return $return_value;
}


/**
 * Echo form for client medification.
 * @param mysqli $mysqli
 */
function get_client_form($mysqli, $type = 'create') {
    $form_data = false;

    if (isset($_POST['client_id'])) {
        if ($account = get_client($mysqli, post_escaped('client_id'))) {
            // display form for client modification
            $form_data = $account;
            $type = 'modify';
        } else {
            // requested item does not exist
            echo "<p class='error'>Requested client does not exist.</p>";
            return;
        }
    } else if (isset($_SESSION['data'])) {
        // form re-fill after not being submitted (errors)
        $form_data = $_SESSION['data'];
    }

    $restrict = false;
    if (user_logged_in() && require_user_level('client', false)) {
        $form_data = get_client($mysqli, $_SESSION['user_id']);
        $type = 'modify';
        $restrict = true;
    }

    ?>
    <form method="post" class="master-form">

        <input type="hidden" name="client_id" value="<?php echo post_escaped('client_id'); ?>"/>

        <fieldset>
            <legend>Osobné údaje</legend>

            <label for="name" class="required">Meno</label>
            <input type="text" name="name" id="name" maxlength="40" value="<?php if ($form_data && isset($form_data['name'])) echo $form_data['name']; ?>" <?php if ($restrict) echo 'readonly';  ?> >

            <label for="surname" class="required">Priezvisko</label>
            <input type="text" name="surname" id="surname" maxlength="40" value="<?php if ($form_data && isset($form_data['surname'])) echo $form_data['surname']; ?>" <?php if ($restrict) echo 'readonly';  ?> >

            <label for="email" class="required">Email</label>
            <input type="text" name="email" id="email" maxlength="40" value="<?php if ($form_data && isset($form_data['email'])) echo $form_data['email']; ?>">
        </fieldset>

        <fieldset>
            <legend>Prihlasovacie údaje</legend>

            <label for="username" class="required">Prihlasovacie meno</label>
            <input type="text" name="username" id="username" maxlength="40" value="<?php if ($form_data && isset($form_data['username'])) echo $form_data['username']; ?>">

            <label for="password" class="required">Heslo<?php if ($type == 'modify') echo ' (prepíše súčasné)'; ?></label>
            <input type="password" name="password" id="password" maxlength="40" value="">
            <label for="password2" class="required">Porvrdenie hesla</label>
            <input type="password" name="password2" id="password2" maxlength="40" value="">
        </fieldset>

        <fieldset>
            <legend>Potvrdenie</legend>
            <?php if ($type == 'create') { ?>
                <button name="create" type="submit">Vytvoriť účet</button>
                <button name="cancel" type="submit">Zrušiť</button>
            <?php } else {?>
                <?php  ?>
                <button name="modify" type="submit">Uložiť zmeny</button>
                <button name="cancel" type="submit">Zrušiť zmeny</button>
                <?php if (!$restrict)  { ?> <button name="delete" type="submit">Odstrániť účet</button> <?php } ?>
            <?php } ?>
        </fieldset>
    </form>
    <?php
}


/**
 * Verify form data, generate errors.
 * @return array form data
 */
function get_client_form_data($type = 'create') {
    $data = array();
    $error = array();

    $restrict = false;
    if (user_logged_in() && require_user_level('client', false)) {
        $restrict = true;
    }

    //osobne udaje
    if (!$restrict && isset($_POST['name'])) {
        $data['name'] = post_escaped('name');
        if ($data['name'] == '') $error['name'] = 'Prosím zadajte meno.';
    }

    if (!$restrict && isset($_POST['surname'])) {
        $data['surname'] = post_escaped('surname');
        if ($data['surname'] == '') $error['surname'] = 'Prosím zadajte priezvisko.';
    }

    $data['email'] = post_escaped('email');

    // prihlasovacie udaje
    if (isset($_POST['username'])) {
        $data['username'] = post_escaped('username');
        if ($data['username'] == '') $error['username'] = 'Prosím zadajte prihlasovacie meno.';
    }

    // do not allow empty password for new account
    if ($type == 'create' && (post_escaped('password') == '')) {
        $error['password'] = 'Prosím zadajte heslo.';
    }

    // check if password and password verificaiion field match
    if (post_escaped('password') != post_escaped("password2")) {
        $error['password_verification'] = "Heslo a potvrdenie hesla sa nezhodujú.";
    } else {
        $data['password'] = post_escaped('password');
    }

    // return data
    $_SESSION['error'] = $error;
    return $data;
}


/**
 * Main controller.
 * @param mysqli $mysqli
 */
function handle_client_modify($mysqli) {
    $request_type = '';
    if (isset($_POST['create'])) $request_type = 'create';
    if (isset($_POST['cancel'])) $request_type = 'cancel';
    if (isset($_POST['modify'])) $request_type = 'modify';
    if (isset($_POST['delete'])) $request_type = 'delete';

    $account_id = 0;
    if (isset($_POST['client_id'])) $account_id = intval(post_escaped('client_id'));

    $restrict = false;
    if (user_logged_in() && require_user_level('client', false)) {
        $account_id = $_SESSION['user_id'];
        $restrict = true;
    }

    if ($request_type) {
        try {
            switch ($request_type) {
                case 'create':
                    // get data
                    if ($restrict) return;
                    $data = get_client_form_data();
                    if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
                        session_result('error', "Účet nebolo možné vytvoriť.");
                        break;
                    }

                    // query
                    if (user_logged_in()) $query = "INSERT INTO client SET author_id=" . $_SESSION['user_id'];
                    else $query = "INSERT INTO client SET author_id=" . 1;
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
                    $data = get_client_form_data('modify');
                    if ((isset($_SESSION['error']) && !empty($_SESSION['error'])) || !$account_id) {
                        session_result('error', "Účet nebolo možné upraviť.");
                        break;
                    }
                    // query
                    $query = "UPDATE client SET author_id=" . $_SESSION['user_id'];
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
                    if ($restrict) return;
                    // query
                    $query = "DELETE FROM client WHERE id=" . $account_id ;
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
                $_SESSION['result_message'] = 'Účet nemožno odstrániť. (Brániť tomu môže napríklad existujúca platba s ňím asociovaná.)';
            }
        } finally {
            if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
                // error in form data -> stay on this page
                if (isset($data)) $_SESSION['data'] = $data;
                header("Location: client-modify.php");
            } else {
                // go to account-overview overview
                unset($_SESSION['data']);

                if ($restrict) header("Location: client-modify.php");
                else if (user_logged_in()) header("Location: client-overview.php?highlight=$account_id");
                else {
                    header("Location: index.php");
                }
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

    get_client_form($mysqli);
}
