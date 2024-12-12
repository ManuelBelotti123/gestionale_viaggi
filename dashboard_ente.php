<?php
include('./auth/auth_functions.php');
include('./db/db.php');

// Verifica se l'utente è loggato
redirect_if_not_logged_in();

// Query per recuperare il nome dell'utente loggato
try {
    $stmt = $conn->prepare("SELECT name FROM gsv_users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->fetch();
    $username = $user['name'];
} catch (PDOException $e) {
    $username = "Utente";
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Utente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <!-- include Navbar -->
    <?php include('./comp/navbar.php'); ?>

    <!-- Hero Section -->
    <div class="hero text-center">
        <h1 class="display-4">Benvenuto, <?= htmlspecialchars($username); ?>!</h1>
        <p class="lead">Esplora il mondo con i nostri itinerari personalizzati.</p>
    </div>

    <!-- Features Section -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Cosa puoi fare?</h2>
        <div class="row g-4">
            <!-- Crea itinerari -->
            <div class="col-md-6">
                <div class="card" style="min-height: 200px; padding:20px;">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="p-2 bi bi-pencil-fill"></i>Crea Itinerario</h5>
                        <p class="card-text">Inserisci itinerari personalizzati per località.</p>
                        <a href="./ente_pages/create_itinerary.php" class="btn btn-primary">Vai</a>
                    </div>
                </div>
            </div>
            <!-- Visualizza itinerari -->
            <div class="col-md-6">
                <div class="card" style="min-height: 200px; padding:20px;">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="p-2 bi bi-eye"></i>Visualizza Itinerari</h5>
                        <p class="card-text">Visualizza i tuoi itinerari e quelli di tutti gli altri enti.</p>
                        <a href="./user_pages/explore.php" class="btn btn-primary">Vai</a>
                    </div>
                </div>
            </div>
            <!-- Itinerari di una località -->
            <div class="col-md-6">
                <div class="card" style="min-height: 200px; padding:20px;">
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="p-2 bi bi-search"></i>Ricerca Itinerari</h5>
                        <p class="card-text">Trova tutti gli itinerari relativi a una specifica località, tappa o tag.</p>
                        <a href="./user_pages/search.php" class="btn btn-primary">Vai</a>
                    </div>
                </div>
            </div>
            <!-- Statistiche itinerari -->
            <div class="col-md-6">
                <div class="card" style="min-height: 200px; padding:20px;">
                    <i></i>
                    <div class="card-body text-center">
                        <h5 class="card-title"><i class="p-2 bi bi-bar-chart"></i>Statistiche Itinerari</h5>
                        <p class="card-text">Visualizza quanti utenti hanno i tuoi itinerari nei preferiti.</p>
                        <a href="./ente_pages/stats.php" class="btn btn-primary">Vai</a>
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
                <li class="list-inline-item"><a href="#">Privacy</a></li>
                <li class="list-inline-item"><a href="#">Termini</a></li>
                <li class="list-inline-item"><a href="#">Contatti</a></li>
            </ul>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

