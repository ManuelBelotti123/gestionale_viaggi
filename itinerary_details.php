<?php
// Include la connessione al database
include('./db/db.php');
include('./auth/auth_functions.php');
session_start();

// Controlla se l'utente è autenticato
redirect_if_not_logged_in();

// Recupera l'ID dell'itinerario dalla query string
$itinerary_id = isset($_GET['id']) ? intval($_GET['id']) : null;

if (!$itinerary_id) {
    echo "ID dell'itinerario non specificato.";
    exit;
}

try {
    // Recupera i dettagli dell'itinerario dal database
    $stmt = $conn->prepare("
        SELECT I.itinerary_id, I.title, I.description, I.created_at, I.is_active, 
               L.name AS location_name, L.region AS location_region, L.country AS location_country,
               U.username AS entity_name
        FROM gsv_itineraries I
        JOIN gsv_locations L ON I.location_id = L.location_id
        JOIN gsv_users U ON I.entity_id = U.user_id
        WHERE I.itinerary_id = :itinerary_id
    ");
    $stmt->bindParam(':itinerary_id', $itinerary_id, PDO::PARAM_INT);
    $stmt->execute();

    $itinerary = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$itinerary) {
        echo "Itinerario non trovato.";
        exit;
    }

    // Recupera le tappe associate all'itinerario
    $stmt_stages = $conn->prepare("
        SELECT stage_id, title, description, `stage_order`
        FROM gsv_stages
        WHERE itinerary_id = :itinerary_id
        ORDER BY `stage_order`
    ");
    $stmt_stages->bindParam(':itinerary_id', $itinerary_id, PDO::PARAM_INT);
    $stmt_stages->execute();

    $stages = $stmt_stages->fetchAll(PDO::FETCH_ASSOC);

    // Controlla se l'utente ha messo like all'itinerario
    $stmt_like = $conn->prepare("
        SELECT COUNT(*) as is_favorite
        FROM gsv_favorites
        WHERE itinerary_id = :itinerary_id AND user_id = :user_id
    ");
    $stmt_like->execute([
        ':itinerary_id' => $itinerary_id,
        ':user_id' => $_SESSION['user_id']
    ]);
    $is_favorite = $stmt_like->fetch(PDO::FETCH_ASSOC)['is_favorite'] > 0;

    // Gestisci l'azione di mettere/togliere like
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($is_favorite) {
            $stmt_remove_favorite = $conn->prepare("
                DELETE FROM gsv_favorites
                WHERE itinerary_id = :itinerary_id AND user_id = :user_id
            ");
            $stmt_remove_favorite->execute([
                ':itinerary_id' => $itinerary_id,
                ':user_id' => $_SESSION['user_id']
            ]);
        } else {
            $stmt_add_favorite = $conn->prepare("
                INSERT INTO gsv_favorites (itinerary_id, user_id, saved_at)
                VALUES (:itinerary_id, :user_id, NOW())
            ");
            $stmt_add_favorite->execute([
                ':itinerary_id' => $itinerary_id,
                ':user_id' => $_SESSION['user_id']
            ]);
        }
        header("Location: itinerary_details.php?id=$itinerary_id");
        exit;
    }
} catch (PDOException $e) {
    echo "Errore durante il recupero dei dati: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dettagli Itinerario - <?= htmlspecialchars($itinerary['title']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body>
    <!-- Navbar -->
    <?php include('./comp/navbar.php'); ?>

    <div class="container mt-5">
        <h1 class="text-center"><?= htmlspecialchars($itinerary['title']) ?></h1>
        <p class="text-center text-muted">
            Creato da: <?= htmlspecialchars($itinerary['entity_name']) ?> <br>
            Località: <?= htmlspecialchars($itinerary['location_name']) ?>, <?= htmlspecialchars($itinerary['location_region']) ?>, <?= htmlspecialchars($itinerary['location_country']) ?>
        </p>
        <p class="text-center text-muted"><small>Data di creazione: <?= htmlspecialchars($itinerary['created_at']) ?></small></p>

        <!-- Descrizione -->
        <div class="row mt-4">
            <div class="col-md-8 offset-md-2">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Descrizione</h5>
                        <p class="card-text"><?= nl2br(htmlspecialchars($itinerary['description'])) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tappe associate -->
        <div class="mt-5">
            <h3 class="text-center mb-4">Tappe</h3>
            <?php if (count($stages) > 0): ?>
                <div class="row">
                    <?php foreach ($stages as $stage): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-body">
                                    <h5 class="card-title"><?= htmlspecialchars($stage['title']) ?></h5>
                                    <p class="card-text"><?= substr(htmlspecialchars($stage['description']), 0, 100) . '...' ?></p>
                                    <p class="text-muted"><small>Ordine: <?= htmlspecialchars($stage['stage_order']) ?></small></p>
                                    <a href="stage_details.php?stage_id=<?= $stage['stage_id'] ?>" class="btn btn-primary">Scopri di più</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-center text-muted">Nessuna tappa disponibile per questo itinerario.</p>
            <?php endif; ?>
        </div>

        <!-- Pulsante per mettere/togliere like -->
        <div class="text-center mt-3">
            <form method="POST">
                <button type="submit" class="btn btn-<?php echo $is_favorite ? 'danger' : 'primary'; ?>">
                    <?php echo $is_favorite ? 'Rimuovi dai Preferiti' : 'Aggiungi ai Preferiti'; ?>
                </button>
            </form>
        </div>

        <!-- Pulsante per tornare indietro -->
        <div class="mt-2 mb-3 text-center">
            <!-- Utilizza JavaScript per tornare indietro alla pagina precedente, evita il conferma invio modulo -->
            <a href="./user_pages/explore.php" class="btn btn-secondary">Torna ad Esplora</a>
        </div>

    </div>

    <!-- Footer -->
    <?php include('./comp/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
