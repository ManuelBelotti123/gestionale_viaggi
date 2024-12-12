<?php
include '../db/db.php';
include '../auth/auth_functions.php';

// Start the session and check user authentication
session_start();
redirect_if_not_logged_in();

// Get itinerary_id from the query string
$itinerary_id = isset($_GET['itinerary_id']) ? (int) $_GET['itinerary_id'] : 0;
if ($itinerary_id <= 0) {
    header("Location: dashboard_ente.php");
    exit;
}

// Add a stage to the itinerary
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_stage'])) {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $stage_order = (int)$_POST['stage_order'];

    try {
        $stmt = $conn->prepare(
            "INSERT INTO gsv_stages (itinerary_id, title, description, `stage_order`) 
             VALUES (:itinerary_id, :title, :description, :stage_order)"
        );
        $stmt->execute([
            ':itinerary_id' => $itinerary_id,
            ':title' => $title,
            ':description' => $description,
            ':stage_order' => $stage_order,
        ]);
        $success = "Tappa aggiunta con successo!";
    } catch (PDOException $e) {
        $error = "Errore nell'aggiunta della tappa: " . $e->getMessage();
    }
}

// Fetch existing stages for the itinerary
try {
    $stmt = $conn->prepare("SELECT * FROM gsv_stages WHERE itinerary_id = :itinerary_id order BY `stage_order`");
    $stmt->execute([':itinerary_id' => $itinerary_id]);
    $stages = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $stages = [];
    $error = "Errore nel recupero delle tappe: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aggiungi Tappe</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .stage-list {
            margin-top: 20px;
        }
        .card:hover {
            transform: scale(1) !important;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .stage-card {
            margin-bottom: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .stage-header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <?php include '../comp/navbar.php'; ?>

    <div class="container mt-5">
        <div class="stage-header">
            <h2>Aggiungi Tappe all'Itinerario</h2>
            <p>Compila i dettagli di ogni tappa e aggiungile all'itinerario.</p>
        </div>

        <div class="card p-4 mb-4">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="title" class="form-label">Titolo della Tappa</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Descrizione</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="stage_order" class="form-label">Ordine</label>
                    <input type="number" class="form-control" id="stage_order" name="stage_order" min="1" required>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <div class="d-flex justify-content-between">
                    <a href="../dashboard_ente.php" class="btn btn-secondary">Annulla</a>
                    <button type="submit" name="add_stage" class="btn btn-primary">Aggiungi Tappa</button>
                </div>
            </form>
        </div>

        <div class="stage-list mb-5">
            <h3>Tappe Esistenti</h3>

            <?php if (count($stages) > 0): ?>
                <?php foreach ($stages as $stage): ?>
                    <div class="card stage-card p-3">
                        <h5><?php echo htmlspecialchars($stage['title']); ?></h5>
                        <p><?php echo htmlspecialchars($stage['description']); ?></p>
                        <span class="badge bg-secondary">Ordine: <?php echo $stage['stage_order']; ?></span>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">Non ci sono tappe aggiunte a questo itinerario.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../comp/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
