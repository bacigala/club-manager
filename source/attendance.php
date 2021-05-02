

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
            <?php get_attendance_of_client_tr($mysqli); ?>
        </table>
    </div>
</section>

<?php
    include('attendance-aside.php');
    include('footer.php');

    /*
     * Support functions
     */

    function get_attendance_of_client_tr($mysqli) {
        // GET UNITS USER IS SIGNED FOR AND ATTENDANCE IS RECORDED
        $query  = " SELECT unit.name, unit.start_datetime, unit.end_datetime, unit_client.present";
        $query .= " FROM unit JOIN unit_client ON (unit.id = unit_client.unit_id) JOIN client ON (unit_client.client_id = client.id) ";
        $query .= " WHERE ";
        $query .= " client.id = " . $_SESSION['user_id'];
        $query .= " AND unit.attendance ";
        $query .= " AND status <> 'restrict' AND status <> 'refuse'  AND status <> 'invite'  AND status <> 'request' AND status <> 'retract' ";
        $query .= " AND (unit.type = 'occurrence' OR unit.type = 'singleevent')"; //only this represents attendance
        $query .= " ORDER BY unit.start_datetime ASC ";

        $result = db_query($mysqli, $query);
        if (!is_null($result) && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $output  = '<tr>';
                $output .= '<td>' . $row['name'] . '</td>';
                $output .= '<td>' . date_format(date_create($row['start_datetime']), "d.m.Y H:i") . ' - ' . date_format(date_create($row['end_datetime']), "d.m.Y H:i") . '</td>';
                $output .= '<td' . ($row['present'] ? '' : ' class="warn"') . '>' . ($row['present'] ? 'Prítomný(á)' : 'Neprítomný(á)') . '</td>';

                $output .= '</tr>';
                echo $output;
            }
            $result->free();
        }
    }

?>
