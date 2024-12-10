<?php
// Include la connessione al database
include('../db/db.php');
include('../auth/auth_functions.php');
session_start();

// Controlla se l'utente è autenticato
redirect_if_not_logged_in();

// Variabili per la gestione della ricerca
$search_type = $_POST['search_type'] ?? 'title';
$search_query = $_POST['search_query'] ?? '';
$itineraries = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($search_query)) {
    try {
        // Seleziona la query SQL in base al tipo di ricerca
        if ($search_type === 'title') {
            $stmt = $conn->prepare("
                SELECT I.itinerary_id, I.title, I.description, L.name AS location_name
                FROM gsv_itineraries I
                JOIN gsv_locations L ON I.location_id = L.location_id
                WHERE I.title LIKE :query AND I.is_active = 1
            ");
        } else if ($search_type === 'location') {
            $stmt = $conn->prepare("
                SELECT I.itinerary_id, I.title, I.description, L.name AS location_name
                FROM gsv_itineraries I
                JOIN gsv_locations L ON I.location_id = L.location_id
                WHERE L.name LIKE :query AND I.is_active = 1
            ");
        } else if ($search_type === 'stage') {
            $stmt = $conn->prepare("
                SELECT DISTINCT I.itinerary_id, I.title, I.description, L.name AS location_name
                FROM gsv_itineraries I
                JOIN gsv_stages S ON I.itinerary_id = S.itinerary_id
                JOIN gsv_locations L ON I.location_id = L.location_id
                WHERE S.title LIKE :query
            ");
        } else if ($search_type === 'tag') {
            $stmt = $conn->prepare("
                SELECT I.itinerary_id, I.title, I.description, L.name AS location_name
                FROM gsv_itineraries I
                JOIN gsv_locations L ON I.location_id = L.location_id
                JOIN gsv_itinerary_tags IT ON I.itinerary_id = IT.itinerary_id
                JOIN gsv_tags T ON IT.tag_id = T.tag_id
                WHERE T.name LIKE :query AND I.is_active = 1
            ");
        }

        // Esegui la query
        $stmt->execute([':query' => '%' . $search_query . '%']);
        $itineraries = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error_message = "Errore durante la ricerca: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ricerca Itinerari</title>
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
        <h1 class="text-center mb-4">Cerca Itinerari</h1>
        
        <!-- Form di Ricerca -->
        <form method="POST" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search_query" class="form-control" placeholder="Inserisci il termine di ricerca" value="<?= htmlspecialchars($search_query) ?>" required>
                </div>
                <div class="col-md-3">
                    <select name="search_type" class="form-select">
                        <option value="title" <?= $search_type === 'title' ? 'selected' : '' ?>>Titolo</option>
                        <option value="location" <?= $search_type === 'location' ? 'selected' : '' ?>>Località</option>
                        <option value="stage" <?= $search_type === 'stage' ? 'selected' : '' ?>>Tappa</option>
                        <option value="tag" <?= $search_type === 'tag' ? 'selected' : '' ?>>Tag</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Cerca</button>
                </div>
            </div>
        </form>

        <!-- Risultati della Ricerca -->
        <?php if (!empty($itineraries)): ?>
            <div class="row">
                <?php foreach ($itineraries as $itinerary): ?>
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
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <p class="text-center text-danger">Nessun risultato trovato per il termine inserito.</p>
        <?php endif; ?>
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

