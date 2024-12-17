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

    // Recupera l'andamento dei preferiti nel tempo
    $stmt_trend = $conn->prepare("
        SELECT i.title, DATE(f.saved_at) as date, COUNT(f.favorite_id) as daily_favorites
        FROM gsv_itineraries i
        LEFT JOIN gsv_favorites f ON i.itinerary_id = f.itinerary_id
        WHERE i.entity_id = :entity_id AND f.saved_at >= :start_date
        GROUP BY i.title, DATE(f.saved_at)
        ORDER BY DATE(f.saved_at)
    ");
    $stmt_trend->execute([
        ':entity_id' => $_SESSION['user_id'],
        ':start_date' => $start_date,
    ]);
    $trend_data = $stmt_trend->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Errore durante il recupero delle statistiche: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiche Itinerari</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .card:hover {
            transform: scale(1) !important;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <?php include('../comp/navbar.php'); ?>

    <div class="container mt-5 flex-grow-1">
        <h1 class="text-center">Statistiche Itinerari</h1>
        <p class="text-center">Visualizza l'andamento dei tuoi itinerari aggiunti ai preferiti dagli utenti.</p>

        <div class="row">
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        Statistiche Preferiti per Itinerario
                    </div>
                    <div class="card-body">
                        <canvas id="favoritesChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card mb-4">
                    <div class="card-header">
                        Andamento Preferiti nel Tempo
                    </div>
                    <div class="card-body">
                        <canvas id="trendChart" width="400" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Dati ricevuti dal backend
        const stats = <?php echo json_encode($stats); ?>;
        const trendData = <?php echo json_encode($trend_data); ?>;

        // Estrai i dati per il grafico a barre
        const labels = stats.map(stat => stat.title);
        const data = stats.map(stat => stat.favorites_count);

        // Configura il grafico a barre
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
                mantainAspectRatio: false,
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

        // Prepara i dati per il grafico a linee
        const trendLabels = [...new Set(trendData.map(item => item.date))];
        const trendDatasets = labels.map((title, index) => {
            return {
                label: title,
                data: trendLabels.map(date => {
                    const item = trendData.find(d => d.title === title && d.date === date);
                    return item ? item.daily_favorites : 0;
                }),
                fill: false,
                borderColor: `rgba(${index * 50}, 99, 132, 1)`,
                tension: 0.1
            };
        });

        // Configura il grafico a linee
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: trendDatasets
            },
            options: {
                mantainAspectRatio: false,
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Andamento Preferiti nel Tempo'
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