<?php		
		function check_user($mysqli, $username, $password, $try_admin = false) {
			if (!$mysqli->connect_errno) {
				$table = $try_admin ? 'account' : 'client'; 
				$sql = "SELECT * FROM $table WHERE username='$username' AND password=SHA2('$password',256)";
				if (($result = $mysqli->query($sql)) && ($result->num_rows > 0)) {
					// query OK
					$row = $result->fetch_assoc();
					$result->free();
					return $row;
				} else {
					// query error || no such user
					if (!$try_admin) return check_user($mysqli, $username, $password, true); // try login as admin
					return false;
				}
			} else {
				// db-server connection error
				return false;
			}
		}


    if (isset($_POST["username"]) && isset($_POST["password"]) && $user = check_user($mysqli, post_escaped('username'), post_escaped('password'))) {
        // login request
				$_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_surname'] = $user['surname'];
				
				if (!isset($user['is_tutor'])) {
					$_SESSION['user_is_client'] = true;
				} else {
					$_SESSION['user_is_client'] = false;
					$_SESSION['user_is_tutor'] = $user['is_tutor'];
					$_SESSION['user_is_accountant'] = $user['is_accountant'];
					$_SESSION['user_is_admin'] = $user['is_admin'];
				}
    } elseif (isset($_POST['logout'])) {
				// logout request 
        session_unset();
        session_destroy();
    }

    $_SESSION['has_user'] = isset($_SESSION['user_id']);
?>