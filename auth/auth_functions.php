<?php
session_start();

// Funzione per verificare se l'utente è loggato
function redirect_if_not_logged_in() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: /gestionale_viaggi/auth/login.php");
        exit();
    }
}

// Funzione per disconnettere l'utente
function logout() {
    session_destroy();
    header("Location: /auth/login.php");
    exit();
}

// Funzione per verificare se l'utente è un ente
function redirect_if_not_entity() {
    if ($_SESSION['user_type'] !== 'ente') {
        header("Location: /gestionale_viaggi/auth/login.php");
        exit();
    }
}

?>
