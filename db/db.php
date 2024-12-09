<?php
//configurazione db
$host = 'localhost';
$dbname = 'my_belottimanuel';
$username = 'belottimanuel';
$password = '';

try {
    //PDO object
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    //PDO Attributes
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    //error
    die("Errore di connessione al database: " . $e->getMessage());
}
?>
