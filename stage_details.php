<?php
// Include la connessione al database
include('./db/db.php');
include('./auth/auth_functions.php');
session_start();

// Controlla se l'utente è autenticato
redirect_if_not_logged_in();

// Recupera l'ID della tappa dalla query string
$stage_id = isset($_GET['stage_id']) ? intval($_GET['stage_id']) : null;

if (!$stage_id) {
    echo "ID della tappa non specificato.";
    exit;
}

try {
    // Recupera i dettagli della tappa dal database
    $stmt = $conn->prepare("
        SELECT S.stage_id, S.title AS stage_title, S.description AS stage_description, S.stage_order, 
               I.title AS itinerary_title, I.itinerary_id, 
               L.name AS location_name
        FROM gsv_stages S
        JOIN gsv_itineraries I ON S.itinerary_id = I.itinerary_id
        JOIN gsv_locations L ON I.location_id = L.location_id
        WHERE S.stage_id = :stage_id
    ");
    $stmt->bindParam(':stage_id', $stage_id, PDO::PARAM_INT);
    $stmt->execute();

    $stage = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$stage) {
        echo "Tappa non trovata.";
        exit;
    }
} catch (PDOException $e) {
    echo "Errore durante il recupero dei dati: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dettagli Tappa - <?= htmlspecialchars($stage['stage_title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <!-- Navbar -->
    <?php include('./comp/navbar.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center"><?= htmlspecialchars($stage['stage_title']) ?></h1>
        <p class="text-center text-muted"><small>Parte dell'itinerario: <a href="itinerary_details.php?id=<?= $stage['itinerary_id'] ?>"><?= htmlspecialchars($stage['itinerary_title']) ?></a></small></p>
        
        <div class="row mt-4 mb-5">
            <div class="col-md-8 offset-md-2">
                <!-- Dettagli della tappa -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Descrizione</h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($stage['stage_description'])) ?></p>
                        
                        <h6 class="card-subtitle mt-4 text-muted">Ordine nella sequenza:</h6>
                        <p class="card-text"><?= htmlspecialchars($stage['stage_order']) ?></p>
                        
                        <h6 class="card-subtitle mt-4 text-muted">Località:</h6>
                        <p class="card-text"><?= htmlspecialchars($stage['location_name']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pulsante per tornare indietro -->
    <div class="mt-2 mb-3 text-center">
        <!-- Utilizza JavaScript per tornare indietro alla pagina precedente, evita il conferma invio modulo -->
        <a href="./user_pages/explore.php" class="btn btn-secondary">Torna ad Esplora</a>
    </div>

    <!-- Footer -->
    <?php include('./comp/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
