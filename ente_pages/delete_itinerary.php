<?php
//elimina l'itinerario
include '../db/db.php';
include '../auth/auth_functions.php';

session_start();

redirect_if_not_logged_in();

$error = "";
$itinerary_id = (int) $_GET['itinerary_id'];

try {
    $stmt = $conn->prepare("DELETE FROM gsv_itineraries WHERE itinerary_id = :itinerary_id");
    $stmt->execute([':itinerary_id' => $itinerary_id]);

    header("Location: create_itinerary.php");
    exit;
} catch (PDOException $e) {
    $error = "Errore durante l'eliminazione dell'itinerario: " . $e->getMessage();
}
?>