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

    // Gli itinerari migliori di sempre
    $stmt_best_all_time = $conn->prepare("
        SELECT I.itinerary_id, I.title, I.description, L.name AS location_name, COUNT(F.favorite_id) AS favorites_count
        FROM gsv_itineraries I
        JOIN gsv_locations L ON I.location_id = L.location_id
        JOIN gsv_favorites F ON I.itinerary_id = F.itinerary_id
        WHERE I.is_active = 1
        GROUP BY I.itinerary_id
        ORDER BY favorites_count DESC
        LIMIT 6
    ");
    $stmt_best_all_time->execute();
    $best_all_time_itineraries = $stmt_best_all_time->fetchAll(PDO::FETCH_ASSOC);

    // Gli itinerari più popolari di questa settimana
    $stmt_popular_week = $conn->prepare("
        SELECT I.itinerary_id, I.title, I.description, L.name AS location_name, COUNT(F.favorite_id) AS favorites_count
        FROM gsv_itineraries I
        JOIN gsv_locations L ON I.location_id = L.location_id
        JOIN gsv_favorites F ON I.itinerary_id = F.itinerary_id
        WHERE I.is_active = 1 AND F.saved_at >= DATE_SUB(CURDATE(), INTERVAL 1 WEEK)
        GROUP BY I.itinerary_id
        ORDER BY favorites_count DESC
        LIMIT 6
    ");
    $stmt_popular_week->execute();
    $popular_week_itineraries = $stmt_popular_week->fetchAll(PDO::FETCH_ASSOC);

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>
    <!-- Navbar -->
    <?php include('../comp/navbar.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center mb-4">Esplora Itinerari</h1>

        <!-- Sezione Ultimi Itinerari Aggiunti -->
        <section class="mb-5">
            <h2 class="mb-3">Ultimi Itinerari Aggiunti</h2>
            <div class="row">
                <?php if (empty($recent_itineraries)): ?>
                    <p class="text-center">Nessun risultato.</p>
                <?php else: ?>
                    <?php foreach ($recent_itineraries as $itinerary): ?>
                        <div class="col-md-6">
                            <div class="card mb-4 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($itinerary['title']) ?></h5>
                                    <p class="card-text">
                                    <?= //solo i primi 100 caratteri della descrizione
                                        htmlspecialchars(substr($itinerary['description'], 0, 100)) . "..."
                                    ?>
                                    </p>
                                    <p class="text-muted"><small>Località: <?= htmlspecialchars($itinerary['location_name']) ?></small></p>
                                    <a href="../itinerary_details.php?id=<?= $itinerary['itinerary_id'] ?>" class="btn btn-primary">Scopri di più</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Sezione Gli Itinerari Migliori di Sempre -->
        <section class="mb-5">
            <h2 class="mb-3">Gli Itinerari Migliori di Sempre</h2>
            <div class="row">
                <?php if (empty($best_all_time_itineraries)): ?>
                    <p class="text-center">Nessun risultato.</p>
                <?php else: ?>
                    <?php foreach ($best_all_time_itineraries as $itinerary): ?>
                        <div class="col-md-6">
                            <div class="card mb-4 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($itinerary['title']) ?></h5>
                                    <p class="card-text">
                                    <?= //solo i primi 100 caratteri della descrizione
                                        htmlspecialchars(substr($itinerary['description'], 0, 100)) . "..."
                                    ?>
                                    </p>
                                    <p class="text-muted"><small>Località: <?= htmlspecialchars($itinerary['location_name']) ?></small></p>
                                    <a href="../itinerary_details.php?id=<?= $itinerary['itinerary_id'] ?>" class="btn btn-primary">Scopri di più</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>

        <!-- Sezione Gli Itinerari Più Popolari di Questa Settimana -->
        <section class="mb-5">
            <h2 class="mb-3">Gli Itinerari Più Popolari di Questa Settimana</h2>
            <div class="row">
                <?php if (empty($popular_week_itineraries)): ?>
                    <p class="text-center">Nessun risultato.</p>
                <?php else: ?>
                    <?php foreach ($popular_week_itineraries as $itinerary): ?>
                        <div class="col-md-6">
                            <div class="card mb-4 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($itinerary['title']) ?></h5>
                                    <p class="card-text">
                                    <?= //solo i primi 100 caratteri della descrizione
                                        htmlspecialchars(substr($itinerary['description'], 0, 100)) . "..."
                                    ?>
                                    </p>
                                    <p class="text-muted"><small>Località: <?= htmlspecialchars($itinerary['location_name']) ?></small></p>
                                    <a href="../itinerary_details.php?id=<?= $itinerary['itinerary_id'] ?>" class="btn btn-primary">Scopri di più</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <?php include('../comp/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>