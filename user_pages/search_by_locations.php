<?php
include('../auth/auth_functions.php');
include('../db/db.php');

// Verifica se l'utente è loggato
redirect_if_not_logged_in();

$search_results = [];
$search_query = "";

// Recupera tutte le località disponibili per il menu a discesa
try {
    $locations_stmt = $conn->query("SELECT * FROM gsv_locations ORDER BY name");
    $locations = $locations_stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recupera la località selezionata o cercata
        $search_query = $_POST['location'] ?? '';
        $stmt = $conn->prepare("
            SELECT I.*, L.name AS location_name 
            FROM gsv_itineraries I
            JOIN gsv_locations L ON I.location_id = L.location_id
            WHERE L.name LIKE :location
        ");
        $stmt->execute([':location' => '%' . $search_query . '%']);
        $search_results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $error_message = "Errore nel caricamento dei dati.";
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ricerca per Località</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./styles/search.css">
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
        <h1 class="display-4">Cerca Itinerari per Località</h1>
        <p class="lead">Trova itinerari disponibili nella tua destinazione preferita!</p>
    </div>

    <!-- Search Section -->
    <div class="container my-5">
        <form method="POST" class="row g-3 justify-content-center mb-5">
            <div class="col-md-6">
                <label for="location" class="form-label">Seleziona o digita una località</label>
                <input type="text" list="location-list" name="location" id="location" class="form-control" value="<?= htmlspecialchars($search_query); ?>" placeholder="Inserisci una località...">
                <datalist id="location-list">
                    <?php foreach ($locations as $location): ?>
                        <option value="<?= htmlspecialchars($location['name']); ?>">
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Cerca</button>
            </div>
        </form>

        <!-- Search Results Section -->
        <div>
            <h2 class="text-center mb-4">Risultati della Ricerca</h2>
            <div class="row g-4">
                <?php if (!empty($search_results)): ?>
                    <?php foreach ($search_results as $itinerary): ?>
                        <div class="col-md-4">
                            <div class="card">
                                <img src="./assets/itineraries/default.jpg" class="card-img-top" alt="Itinerario">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($itinerary['title']); ?></h5>
                                    <p class="card-text"><?= htmlspecialchars(substr($itinerary['description'], 0, 100)); ?>...</p>
                                    <p><strong>Località: <?= htmlspecialchars($itinerary['location_name']); ?></strong></p>
                                    <a href="itinerary_details.php?id=<?= $itinerary['itinerary_id']; ?>" class="btn btn-primary">Scopri di più</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center">Nessun itinerario trovato per questa località.</p>
                <?php endif; ?>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
