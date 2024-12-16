<?php
// pagina di profilo dell'utente
include('../db/db.php');
include('../auth/auth_functions.php');
session_start();

// Controlla se l'utente è autenticato
redirect_if_not_logged_in();

// Recupera l'ID dell'utente dalla sessione
$user_id = $_SESSION['user_id'];

try {
    // Recupera i dettagli dell'utente dal database
    $stmt = $conn->prepare("SELECT username, email, name, surname FROM gsv_users WHERE user_id = :user_id");
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<div class='alert alert-danger text-center mt-5'>Utente non trovato.</div>";
        exit;
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger text-center mt-5'>Errore durante il recupero dei dati: " . $e->getMessage() . "</div>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilo Utente</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navbar -->
    <?php include('../comp/navbar.php'); ?>

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Benvenuto, <?php echo htmlspecialchars($user['username']); ?>!</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <tr>
                                <th>Nome</th>
                                <td><?php echo htmlspecialchars($user['name']); ?></td>
                            </tr>
                            <tr>
                                <th>Cognome</th>
                                <td><?php echo htmlspecialchars($user['surname']); ?></td>
                            </tr>
                            <tr>
                                <th>Email</th>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer text-center">
                        <a href="edit_profile.php" class="btn btn-warning">Modifica Profilo</a>
                    </div>
                </div>
            </div>
        </div>

        
    </div>

    <!-- footer -->
    <?php include('../comp/footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
