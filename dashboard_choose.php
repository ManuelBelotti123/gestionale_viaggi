<?php
//se l'utente è base, indirizzato alla dashboard_user, se è ente alla dashboard_ente
include('./db/db.php');
include('./auth/auth_functions.php');
redirect_if_not_logged_in();
if ($_SESSION['user_type'] == 'base') {
    header('Location: dashboard_user.php');
} else {
    header('Location: dashboard_ente.php');
}
?>