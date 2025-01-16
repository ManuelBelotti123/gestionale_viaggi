<?php
include '../db/db.php';
include '../auth/auth_functions.php';

session_start();

redirect_if_not_logged_in();

$error = "";
$itinerary_id = (int) $_GET['itinerary_id'];
$title = $description = $location_id = $tags = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_itinerary'])) {
    $itinerary_id = (int) $_POST['itinerary_id'];
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $location_id = (int) $_POST['location_id'];
    $tags = htmlspecialchars($_POST['tags']);

    try {
        $stmt = $conn->prepare("
            UPDATE gsv_itineraries 
            SET title = :title, description = :description, location_id = :location_id
            WHERE itinerary_id = :itinerary_id
        ");
        $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':location_id' => $location_id,
            ':itinerary_id' => $itinerary_id,
        ]);

        header("Location: ../dashboard_ente.php");
        exit;
    } catch (PDOException $e) {
        $error = "Errore durante la modifica dell'itinerario: " . $e->getMessage();
    }
} else {
    // Fetch existing itinerary data
    $stmt = $conn->prepare("SELECT title, description, location_id FROM gsv_itineraries WHERE itinerary_id = :itinerary_id");
    $stmt->execute([':itinerary_id' => $itinerary_id]);
    $itinerary = $stmt->fetch();

    if ($itinerary) {
        $title = $itinerary['title'];
        $description = $itinerary['description'];
        $location_id = $itinerary['location_id'];
    } else {
        $error = "Itinerario non trovato.";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <title>Modifica itinerario</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <?php include '../includes/navbar.php'; ?>
    <div class="container mt-5">
        <h1 class="mb-4">Modifica itinerario</h1>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form action="" method="post">
            <input type="hidden" name="itinerary_id" value="<?php echo $itinerary_id; ?>">
            <div class="form-group">
                <label for="title">Titolo</label>
                <input type="text" name="title" id="title" class="form-control" required value="<?php echo $title; ?>">
            </div>
            <div class="form-group">
                <label for="description">Descrizione</label>
                <textarea name="description" id="description" class="form-control" required><?php echo $description; ?></textarea>
            </div>
            <div class="form-group">
                <label for="location_id">Località</label>
                <select name="location_id" id="location_id" class="form-control" required>
                    <option value="">Seleziona una località</option>
                    <?php
                    $stmt = $conn->prepare("SELECT location_id, name FROM gsv_locations");
                    $stmt->execute();
                    $locations = $stmt->fetchAll();
                    foreach ($locations as $location) {
                        $selected = $location_id == $location['location_id'] ? 'selected' : '';
                        echo "<option value='{$location['location_id']}' $selected>{$location['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" name="edit_itinerary" class="btn btn-primary">Modifica itinerario</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>