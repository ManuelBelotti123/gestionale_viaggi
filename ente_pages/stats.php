<?php
include '../db/db.php';
include '../auth/auth_functions.php';

session_start();
redirect_if_not_logged_in();

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-1 month'));

try {
    // Recupera le statistiche degli itinerari
    $stmt = $conn->prepare("
        SELECT i.title, COUNT(f.favorite_id) as favorites_count
        FROM gsv_itineraries i
        LEFT JOIN gsv_favorites f ON i.itinerary_id = f.itinerary_id
        WHERE i.entity_id = :entity_id AND (f.saved_at >= :start_date OR f.saved_at IS NULL)
        GROUP BY i.itinerary_id, i.title
        ORDER BY favorites_count DESC
    ");
    $stmt->execute([
        ':entity_id' => $_SESSION['user_id'],
        ':start_date' => $start_date,
    ]);
    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Errore durante il recupero delle statistiche: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiche Itinerari</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <!-- Navbar -->
    <?php include('../comp/navbar.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center">Statistiche Itinerari</h1>
        <p class="text-center">Visualizza l'andamento dei tuoi itinerari aggiunti ai preferiti dagli utenti.</p>

        <!-- Filtro temporale -->
        <form method="GET" class="mb-4">
            <label for="start_date" class="form-label">Filtra per data:</label>
            <div class="input-group">
                <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo $start_date; ?>">
                <button type="submit" class="btn btn-primary">Applica Filtro</button>
            </div>
        </form>

        <!-- Grafico -->
        <canvas class="mb-5" id="favoritesChart" width="400" height="200"></canvas>
    </div>

    <script>
        // Dati ricevuti dal backend
        const stats = <?php echo json_encode($stats); ?>;

        // Estrai i dati per il grafico
        const labels = stats.map(stat => stat.title);
        const data = stats.map(stat => stat.favorites_count);

        // Configura il grafico
        const ctx = document.getElementById('favoritesChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Numero di Preferiti',
                    data: data,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Statistiche Preferiti per Itinerario'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>

    <!-- Footer -->
    <?php include('../comp/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
