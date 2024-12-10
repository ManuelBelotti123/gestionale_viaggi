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
    <link rel="stylesheet" href="./styles/dashboard.css">
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
                        <a class="nav-link active" href="dashboard_user.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="bg-light py-5 text-center">
        <h1 class="display-4">Benvenuto, <?= htmlspecialchars($username); ?>!</h1>
        <p class="lead">Esplora il mondo con i nostri itinerari personalizzati.</p>
    </div>

    <!-- Features Section -->
    <div class="container my-5">
        <h2 class="text-center mb-4">Cosa puoi fare?</h2>
        <div class="row g-4">
            <!-- Esplora tutti gli itinerari -->
            <div class="col-md-6">
                <div class="card">
                    <img src="./img/explore.jpg" class="card-img-top" alt="Esplora Itinerari">
                    <div class="card-body text-center">
                        <h5 class="card-title">Esplora Itinerari</h5>
                        <p class="card-text">Sfoglia tutti gli itinerari disponibili e scopri nuove destinazioni.</p>
                        <a href="./user_pages/explore.php" class="btn btn-primary">Vai</a>
                    </div>
                </div>
            </div>
            <!-- Itinerari di una località -->
            <div class="col-md-6">
                <div class="card">
                    <img src="./img/location.jpg" class="card-img-top" alt="Itinerari Località">
                    <div class="card-body text-center">
                        <h5 class="card-title">Ricerca Itinerari</h5>
                        <p class="card-text">Trova tutti gli itinerari relativi a una specifica località, tappa o tag.</p>
                        <a href="./user_pages/search.php" class="btn btn-primary">Vai</a>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
