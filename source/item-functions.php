<?php

/**
 * Produces <tr>s of the table on item-overview.php page.
 * @param mysqli $mysqli
 * @param int $highlight_id id of te item <tr> to highlight
 */
function get_item_list($mysqli, $highlight_id = 0) {
    $query  = "SELECT item.id, item.name, item.price, item.start_date, item.end_date, unit.name AS 'unit_name'"
                . " FROM item LEFT JOIN unit ON (item.unit_id = unit.id)"
                . " ORDER BY name ASC, start_date DESC";
    $result = db_query($mysqli, $query);
    if (!is_null($result) && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $output  = '<tr' . ($highlight_id == $row['id'] ? ' class="highlight"' : '') . '>';
            $output .= '<td>' . $row['name'] . '</td>';
            $output .= '<td>' . number_format($row['price'], 2, ',', ' ') . ' €</td>';
            $output .= '<td>' . (isset($row['unit_name']) ? $row['unit_name'] : "")  . '</td>';
            $output .= '<td>' . (isset($row['start_date']) ? date_format(date_create($row['start_date']), "d.m.Y") : "")  . '</td>';
            $output .= '<td>' . (isset($row['end_date']) ? date_format(date_create($row['end_date']), "d.m.Y") : "")  . '</td>';

            // options
            $output .= '<td><form method="post" class="table-form" action="item-modify.php">';
            $output .= '<input type="hidden" name="item_id" value="' . $row['id'] . '" />';
            $output .= '<button name="request_type" type="submit" value="modify" class="main-form-option-button">Upraviť</button>';
            $output .= '</form></td>';

            $output .= '</tr>';
            echo $output;
        }
        $result->free();
    }
}

/**
 * Ferch one item record from DB.
 * @param mysqli $mysqli
 * @param int $item_id
 * @return array|string|null
 */
function get_item($mysqli, $item_id = 0) {
    $return_value = '';
    $query = "SELECT * FROM item WHERE id=?";
    if ($statement = $mysqli->prepare($query)) {
        $statement->bind_param("i", $item_id);
        $statement->execute();
        $result = $statement->get_result();
        if ($result->num_rows > 0)
            $return_value = $result->fetch_assoc();
    }
    return $return_value;
}

// construct form for item create / modify
function get_item_form($mysqli, $type = 'create', $form_data = false) {
    if (isset($_POST['request_type'])) $type = post_escaped('request_type');

    if (is_bool($form_data)) {
        if (isset($_POST['item_id'])) {
            if ($item = get_item($mysqli, post_escaped('item_id'))) {
                // display form for item modification
                $form_data = $item;
            } else {
                // requested item does not exist
                echo "<p class='error'>Requested item does not exist.</p>";
                return;
            }
        }
    }

    ?>
    <form method="post" class="master-form">
        <fieldset>
            <legend>Položka</legend>

            <input type="hidden" name="item_id" value="<?php if (isset($_POST['item_id'])) echo post_escaped('item_id'); ?>"/>

            <label for="name" class="required">Názov</label>
            <input type="text" name="name" id="name" maxlength="40" value="<?php if (isset($form_data['name'])) echo $form_data['name']; ?>">

            <label for="price" class="required">Cena</label>
            <input type="number" name="price" id="price" value="<?php if (isset($form_data['price'])) echo $form_data['price']; ?>" min="0" step="0.01">

            <label for="delay" class="required">Zaplatiť do (počet dní)</label>
            <input type="number" name="delay" id="delay" value="<?php if (isset($form_data['delay'])) echo $form_data['delay']; ?>" min="0" step="1">
        </fieldset>

        <fieldset>
            <legend>Asociovaná skupina / udalosť</legend>

            <label for="unit_id">Skupina / Udalosť</label>
            <select id="unit_id" name="unit_id">
                <?php if (!isset($form_data['unit_id']) || $type != 'modify') { ?><option value="0" <?php if (!isset($form_data['unit_id'])) echo 'selected';?> >Žiadna</option><?php } ?>
                <?php
                    $unit_id = isset($form_data['unit_id']) ? $form_data['unit_id'] : false;
                     get_unit_options($mysqli, $unit_id);
                ?>
            </select>

            <label for="start_date" class="required">Od</label>
            <input type="date" name="start_date" id="start_date" min="1900-01-01" value="<?php if (isset($form_data['start_date'])) echo $form_data['start_date']; ?>">

            <label for="end_date" class="required">Do</label>
            <input type="date" name="end_date" id="end_date" min="1900-01-01" value="<?php if (isset($form_data['end_date'])) echo $form_data['end_date']; ?>">
        </fieldset>

        <fieldset>
            <legend>Potvrdenie</legend>
            <?php if ($type == 'create') { ?>
                <button name="item_create" type="submit">Vytvoriť položku</button>
                <button name="item_cancel" type="submit">Zrušiť</button>
            <?php } ?>
            <?php if ($type == 'modify') { ?>
                <button name="item_modify" type="submit">Uložiť zmeny</button>
                <button name="item_cancel" type="submit">Zrušiť zmeny</button>
                <button name="item_delete" type="submit">Odstrániť položku</button>
            <?php } ?>
        </fieldset>
    </form>
    <?php
}

function get_item_form_data() {
    // get & verify data
    $data = array();
    $error = array();

    if (isset($_POST['name'])) {
        $data['name'] = post_escaped('name');
        if ($data['name'] == '') $error['name'] = 'Prosím zadajte názov.';
    }

    if (isset($_POST['price'])) {
        $data['price'] =  intval(post_escaped('price'));
        if ($data['price'] < 0) $error['price'] = 'Cena musí byť nezáporná.';
    }

    if ((strtotime(post_escaped('start_date')) != '0000-00-00') && (strtotime(post_escaped('start_date')) != '')) {
        $data['start_date'] = post_escaped('start_date');
    }
    if ((strtotime(post_escaped('end_date')) != '0000-00-00') && (strtotime(post_escaped('end_date')) != '')) {
        $data['end_date'] = post_escaped('end_date');
    }

    if (isset($_POST['unit_id']) && post_escaped('unit_id')) {
        $data['unit_id'] = post_escaped('unit_id');
    } else {
        // withoud associated event, do not take start and end into account
        unset($data['start_date']);
        unset($data['end_date']);
    }

    $data['delay'] = intval(post_escaped('delay'));

    // return data
    $_SESSION['error'] = $error;
    return $data;
}

function handle_item_modify($mysqli) {
    $request_type = '';
    if (isset($_POST['item_create'])) $request_type = 'create';
    if (isset($_POST['item_cancel'])) $request_type = 'cancel';
    if (isset($_POST['item_modify'])) $request_type = 'modify';
    if (isset($_POST['item_delete'])) $request_type = 'delete';

    $item_id = 0;
    if (isset($_POST['item_id'])) $item_id = intval(post_escaped('item_id'));

    if ($request_type) {
        try {
            switch ($request_type) {
                case 'create': // CREATE
                    // get & verify data from user
                    $data = get_item_form_data();
                    if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
                        session_result('error', "Položku nebolo možné vytvoriť.");
                        break;
                    }

                    // DB insert
                    $query = "INSERT INTO item SET author_id=" . $_SESSION['user_id'];
                    foreach ($data AS $key => $value) $query .= ", $key='$value'";
                    if (!$mysqli->query($query)) {
                        session_result('error', "Položku nebolo možné vytvoriť.");
                        break;
                    }
                    $item_id = $mysqli->insert_id;
                    session_result('success', "Položka bola vytvorená.");
                    break;
                case 'cancel': // CANCEL
                    session_result('warning', 'Položka nebola upravená.');
                    break;
                case 'modify': // MODIFY
                    // get data
                    $data = get_item_form_data();
                    if ((isset($_SESSION['error']) && !empty($_SESSION['error'])) || !$item_id) {
                        session_result('error', "Položku nebolo možné upraviť.");
                        break;
                    }
                    // query
                    $query = "UPDATE item SET author_id=" . $_SESSION['user_id'];
                    foreach ($data AS $key => $value) $query .= ", $key='$value'";
                    $query .= ' WHERE id=' . $item_id ;
                    if (!$mysqli->query($query)) {
                        session_result('error', "Položku nebolo možné upraviť.");
                        break;
                    }

                    session_result('success', 'Položka bola upravená.');
                    break;
                case 'delete': // DELETE
                    // query
                    $query = "DELETE FROM item WHERE id=" . $item_id ;
                    if (!$mysqli->query($query)) {
                        session_result('error', "Položku nebolo možné odstrániť.");
                        break;
                    }
                    session_result('success', 'Položka bola odstránená.');
                    break;
            }
        } catch (mysqli_sql_exception $exception) {
            session_result('error', 'Akciu sa nepodarilo vykonať. (exception)' . $exception);
            if ($request_type == 'delete') {
                $_SESSION['result_message'] = 'Položku nemožno odstrániť. (Brániť tomu môže napríklad existujúca platba s ňou asociovaná.)';
            }
        } finally {
            //$_SESSION['result_message'] .= $mysqli->error; // DEBUG
            if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
                // stay on this page
                header("Location: item-modify.php");
            } else {
                // go to item overview
                header("Location: item-overview.php?highlight=$item_id");
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

    get_item_form($mysqli);
}
