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
    <style>
        body {
            background-color: #f8f9fa;
        }
        .navbar {
            background: #14213d;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .hero {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: #fff;
            padding: 4rem 0;
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: bold;
        }
        .hero p {
            font-size: 1.2rem;
        }
        .card {
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
        }
        footer {
            background: #343a40;
            color: #fff;
        }
        footer a {
            text-decoration: none;
            color: #17a2b8;
        }
        footer a:hover {
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark shadow">
        <div class="container">
            <!-- Logo -->
            <a class="navbar-brand d-flex align-items-center" href="#">
                <i class="p-2 bi bi-globe-central-south-asia"></i>
                <span class="fw-bold">Travel Manager</span>
            </a>
            <!-- Toggle button for mobile view -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <!-- Navbar links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard_choose.php">Dashboard</a>
                    </li>
                </ul>
            </div>
            <!-- Profile dropdown -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-light text-decoration-none dropdown-toggle" id="profileDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="p-2 bi bi-person-circle"></i>
                    <span><?= htmlspecialchars($username); ?></span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end dropdown-menu-dark" aria-labelledby="profileDropdown">
                    <li><a class="dropdown-item" href="profile.php">Profilo</a></li>
                    <li><a class="dropdown-item" href="settings.php">Impostazioni</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="auth/logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero text-center">
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

