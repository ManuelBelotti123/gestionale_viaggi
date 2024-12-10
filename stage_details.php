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
        SELECT S.stage_id, S.title AS stage_title, S.description AS stage_description, S.order, 
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
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Travel Manager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="../dashboard_choose.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="text-center"><?= htmlspecialchars($stage['stage_title']) ?></h1>
        <p class="text-center text-muted"><small>Parte dell'itinerario: <a href="itinerary_details.php?id=<?= $stage['itinerary_id'] ?>"><?= htmlspecialchars($stage['itinerary_title']) ?></a></small></p>
        
        <div class="row mt-4">
            <div class="col-md-8 offset-md-2">
                <!-- Dettagli della tappa -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Descrizione</h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($stage['stage_description'])) ?></p>
                        
                        <h6 class="card-subtitle mt-4 text-muted">Ordine nella sequenza:</h6>
                        <p class="card-text"><?= htmlspecialchars($stage['order']) ?></p>
                        
                        <h6 class="card-subtitle mt-4 text-muted">Località:</h6>
                        <p class="card-text"><?= htmlspecialchars($stage['location_name']) ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4">
        <div class="container text-center">
            <p>&copy; 2024 Travel Manager. Tutti i diritti riservati.</p>
            <ul class="list-inline">
                <li class="list-inline-item"><a href="#" class="text-light">Privacy</a></li>
                <li class="list-inline-item"><a href="#" class="text-light">Termini</a></li>
                <li class="list-inline-item"><a href="#" class="text-light">Contatti</a></li>
            </ul>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
