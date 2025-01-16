<?php
include '../db/db.php';
include '../auth/auth_functions.php';

// Start the session and check user authentication
session_start();
redirect_if_not_logged_in();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_itinerary'])) {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $location_id = (int) $_POST['location_id'];

    try {
        $stmt = $conn->prepare("
            INSERT INTO gsv_itineraries (title, description, entity_id, location_id, created_at, is_active)
            VALUES (:title, :description, :entity_id, :location_id, NOW(), 1)
        ");
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':entity_id' => $_SESSION['user_id'],
            ':location_id' => $location_id,
        ]);
        $itinerary_id = $conn->lastInsertId();

        // Add tags to the itinerary
        if (!empty($_POST['tags'])) {
            $tags = explode(",", $_POST['tags']);
            $tags = array_map('trim', $tags);
            $tags = array_unique($tags);

            foreach ($tags as $tag) {
                $tag = htmlspecialchars($tag);
                $stmt_tag = $conn->prepare("SELECT tag_id FROM gsv_tags WHERE name = :name");
                $stmt_tag->execute([':name' => $tag]);
                $tag_id = $stmt_tag->fetchColumn();

                if (!$tag_id) {
                    $stmt_tag = $conn->prepare("INSERT INTO gsv_tags (name) VALUES (:name)");
                    $stmt_tag->execute([':name' => $tag]);
                    $tag_id = $conn->lastInsertId();
                }

                $stmt_it = $conn->prepare("INSERT INTO gsv_itinerary_tags (itinerary_id, tag_id) VALUES (:itinerary_id, :tag_id)");
                $stmt_it->execute([':itinerary_id' => $itinerary_id, ':tag_id' => $tag_id]);
            }
        }

        // Redirect to the add stages step
        header("Location: add_stages.php?itinerary_id=" . $itinerary_id);
        exit;
    } catch (PDOException $e) {
        $error = "Errore nella creazione dell'itinerario: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crea Nuovo Itinerario</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .step-container {
            margin-top: 20px;
        }
        .card:hover {
            transform: scale(1) !important;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .card-step {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .step-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn {
            margin: 5px 0px;
        }
    </style>
</head>
<body class="d-flex flex-column min-vh-100">
    <!-- Navbar -->
    <?php include '../comp/navbar.php'; ?>

    <div class="container mt-5 mb-5 flex-grow-1">
        <div class="step-header">
            <h2>Crea un Nuovo Itinerario</h2>
            <p>Completa il modulo per creare un nuovo itinerario. Una volta creato, potrai aggiungere tappe e dettagli.</p>
        </div>

        <div class="card card-step p-4">
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="title" class="form-label">Titolo dell'Itinerario</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Descrizione</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>

                <div class="mb-3">
                    <label for="location_id" class="form-label">Località Principale</label>
                    <select class="form-select" id="location_id" name="location_id" required>
                        <?php
                        try {
                            $stmt = $conn->query("SELECT * FROM gsv_locations");
                            $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
                            if ($locations) {
                                foreach ($locations as $location) {
                                    echo "<option value='{$location['location_id']}'>{$location['name']}, {$location['region']}, {$location['country']}</option>";
                                }
                            } else {
                                echo "<option disabled>Nessuna località trovata</option>";
                            }
                        } catch (PDOException $e) {
                            echo "<option disabled>Errore nella query: " . $e->getMessage() . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Permetti di inserire quanti tag si vuole -->
                <div class="mb-3">
                    <label for="tags" class="form-label">Tag (opzionale)</label>
                    <input type="text" class="form-control" id="tags" name="tags" placeholder="Separare i tag con una virgola">
                </div>

                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="d-flex justify-content-between">
                    <a href="../dashboard_ente.php" class="btn btn-secondary">Annulla</a>
                    <button type="submit" name="create_itinerary" class="btn btn-primary">Crea Itinerario</button>
                </div>
            </form>

            <!-- select di tutti i propri itinerari, con possibilità di modifica e delete -->
            <div class="step-container mt-5">
                <h3>Itinerari Esistenti</h3>

                <?php
                try {
                    $stmt = $conn->prepare("SELECT * FROM gsv_itineraries WHERE entity_id = :entity_id");
                    $stmt->execute([':entity_id' => $_SESSION['user_id']]);
                    $itineraries = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($itineraries) {
                        foreach ($itineraries as $itinerary) {
                            echo "<div class='card card-step p-3 mt-3'>";
                            echo "<h5>{$itinerary['title']}</h5>";
                            echo "<p>{$itinerary['description']}</p>";
                            //bottone per modificare l'itinerario
                            echo "<a href='edit_itinerary.php?itinerary_id={$itinerary['itinerary_id']}' class='btn btn-primary'>Modifica</a>";
                            //inserire il codice per eliminare l'itinerario
                            echo "<a href='delete_itinerary.php?itinerary_id={$itinerary['itinerary_id']}' class='btn btn-danger'>Elimina</a>";
                            echo "</div>";
                        }
                    } else {
                        echo "<p class='text-muted'>Non ci sono itinerari creati.</p>";
                    }
                } catch (PDOException $e) {
                    echo "<p class='text-danger'>Errore nella query: " . $e->getMessage() . "</p>";
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../comp/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>