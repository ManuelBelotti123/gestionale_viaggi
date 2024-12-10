<?php
// Include la connessione al database
include('../db/db.php');
include('../auth/auth_functions.php');
session_start();

// Controlla se l'utente è autenticato
redirect_if_not_logged_in();

// Recupera gli itinerari dal database
try {
    // Itinerari più popolari (esempio basato sull'attività recente)
    $stmt_popular = $conn->prepare("
        SELECT I.itinerary_id, I.title, I.description, L.name AS location_name
        FROM gsv_itineraries I
        JOIN gsv_locations L ON I.location_id = L.location_id
        WHERE I.is_active = 1
        ORDER BY I.created_at DESC
        LIMIT 6
    ");
    $stmt_popular->execute();
    $popular_itineraries = $stmt_popular->fetchAll(PDO::FETCH_ASSOC);

    // Ultimi itinerari aggiunti
    $stmt_recent = $conn->prepare("
        SELECT I.itinerary_id, I.title, I.description, L.name AS location_name
        FROM gsv_itineraries I
        JOIN gsv_locations L ON I.location_id = L.location_id
        WHERE I.is_active = 1
        ORDER BY I.created_at DESC
        LIMIT 6
    ");
    $stmt_recent->execute();
    $recent_itineraries = $stmt_recent->fetchAll(PDO::FETCH_ASSOC);

    // Itinerari suggeriti (esempio casuale)
    $stmt_suggested = $conn->prepare("
        SELECT I.itinerary_id, I.title, I.description, L.name AS location_name
        FROM gsv_itineraries I
        JOIN gsv_locations L ON I.location_id = L.location_id
        WHERE I.is_active = 1
        ORDER BY RAND()
        LIMIT 6
    ");
    $stmt_suggested->execute();
    $suggested_itineraries = $stmt_suggested->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Errore durante il recupero degli itinerari: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esplora Itinerari</title>
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
        <h1 class="text-center mb-4">Esplora Itinerari</h1>

        <!-- Sezione Itinerari Popolari -->
        <section class="mb-5">
            <h2 class="mb-3">Itinerari Popolari</h2>
            <div class="row">
                <?php foreach ($popular_itineraries as $itinerary): ?>
                    <div class="col-md-4">
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($itinerary['title']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($itinerary['description']) ?></p>
                                <p class="text-muted"><small>Località: <?= htmlspecialchars($itinerary['location_name']) ?></small></p>
                                <a href="../itinerary_details.php?id=<?= $itinerary['itinerary_id'] ?>" class="btn btn-primary">Scopri di più</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Sezione Ultimi Itinerari Aggiunti -->
        <section class="mb-5">
            <h2 class="mb-3">Ultimi Itinerari Aggiunti</h2>
            <div class="row">
                <?php foreach ($recent_itineraries as $itinerary): ?>
                    <div class="col-md-4">
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($itinerary['title']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($itinerary['description']) ?></p>
                                <p class="text-muted"><small>Località: <?= htmlspecialchars($itinerary['location_name']) ?></small></p>
                                <a href="itinerary_details.php?id=<?= $itinerary['itinerary_id'] ?>" class="btn btn-primary">Scopri di più</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Sezione Itinerari Suggeriti -->
        <section class="mb-5">
            <h2 class="mb-3">Suggeriti per Te</h2>
            <div class="row">
                <?php foreach ($suggested_itineraries as $itinerary): ?>
                    <div class="col-md-4">
                        <div class="card mb-4 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($itinerary['title']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($itinerary['description']) ?></p>
                                <p class="text-muted"><small>Località: <?= htmlspecialchars($itinerary['location_name']) ?></small></p>
                                <a href="itinerary_details.php?id=<?= $itinerary['itinerary_id'] ?>" class="btn btn-primary">Scopri di più</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
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
