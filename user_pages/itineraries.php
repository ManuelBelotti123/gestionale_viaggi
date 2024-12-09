<?php
include('../auth/auth_functions.php');
include('../db/db.php');

// Verifica se l'utente è loggato
redirect_if_not_logged_in();

try {
    // Recupero degli itinerari più popolari (simuliamo ordinamento per popolarità)
    $popular_stmt = $conn->query("SELECT * FROM gsv_itineraries ORDER BY created_at DESC LIMIT 6");
    $popular_itineraries = $popular_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recupero degli itinerari recenti
    $recent_stmt = $conn->query("SELECT * FROM gsv_itineraries ORDER BY created_at DESC LIMIT 6");
    $recent_itineraries = $recent_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recupero delle località più rappresentate negli itinerari
    $location_stmt = $conn->query("
        SELECT L.name, L.region, COUNT(I.itinerary_id) AS itinerary_count
        FROM GSV_locations L
        JOIN GSV_itineraries I ON I.location_id = L.location_id
        GROUP BY L.name, L.region
        ORDER BY itinerary_count DESC
        LIMIT 6
    ");
    $popular_locations = $location_stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $popular_itineraries = [];
    $recent_itineraries = [];
    $popular_locations = [];
    $error_message = "Errore nel caricamento degli itinerari.";
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Esplora Itinerari</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./styles/itineraries.css">
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
                        <a class="nav-link active" href="../dashboard_user.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="bg-dark text-light text-center py-5">
        <h1 class="display-4">Esplora gli Itinerari</h1>
        <p class="lead">Scopri i percorsi migliori per il tuo prossimo viaggio!</p>
    </div>

    <!-- Main Content -->
    <div class="container my-5">
        <!-- Popolari Section -->
        <h2 class="text-center mb-4">Itinerari Popolari</h2>
        <div class="row g-4">
            <?php if (!empty($popular_itineraries)): ?>
                <?php foreach ($popular_itineraries as $itinerary): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <img src="./assets/itineraries/default.jpg" class="card-img-top" alt="Itinerario">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($itinerary['title']); ?></h5>
                                <p class="card-text"><?= htmlspecialchars(substr($itinerary['description'], 0, 100)); ?>...</p>
                                <a href="itinerary_details.php?id=<?= $itinerary['itinerary_id']; ?>" class="btn btn-primary">Scopri di più</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">Nessun itinerario popolare disponibile al momento.</p>
            <?php endif; ?>
        </div>

        <!-- Recenti Section -->
        <h2 class="text-center mt-5 mb-4">Ultimi Itinerari</h2>
        <div class="row g-4">
            <?php if (!empty($recent_itineraries)): ?>
                <?php foreach ($recent_itineraries as $itinerary): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <img src="./assets/itineraries/default.jpg" class="card-img-top" alt="Itinerario">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($itinerary['title']); ?></h5>
                                <p class="card-text"><?= htmlspecialchars(substr($itinerary['description'], 0, 100)); ?>...</p>
                                <a href="itinerary_details.php?id=<?= $itinerary['itinerary_id']; ?>" class="btn btn-primary">Scopri di più</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">Nessun itinerario recente disponibile al momento.</p>
            <?php endif; ?>
        </div>

        <!-- Località Popolari Section -->
        <h2 class="text-center mt-5 mb-4">Località Popolari</h2>
        <div class="row g-4">
            <?php if (!empty($popular_locations)): ?>
                <?php foreach ($popular_locations as $location): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body text-center">
                                <h5 class="card-title"><?= htmlspecialchars($location['name']); ?></h5>
                                <p class="card-text"><?= htmlspecialchars($location['region']); ?></p>
                                <p><strong><?= $location['itinerary_count']; ?> Itinerari</strong></p>
                                <a href="location_itineraries.php?name=<?= urlencode($location['name']); ?>" class="btn btn-primary">Esplora</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-center">Nessuna località popolare disponibile al momento.</p>
            <?php endif; ?>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
