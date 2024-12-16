<?php
// pagina di modifica del profilo
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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['update_profile'])) {
            // Recupera i nuovi dati dal form
            $name = $_POST['name'] ?? '';
            $surname = $_POST['surname'] ?? '';
            $email = $_POST['email'] ?? '';

            // Valida i dati
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error_message = "L'indirizzo email non è valido.";
            } elseif (empty($name) || empty($surname)) {
                $error_message = "Tutti i campi sono obbligatori.";
            } else {
                // Aggiorna i dettagli dell'utente nel database
                $stmt = $conn->prepare("UPDATE gsv_users SET name = :name, surname = :surname, email = :email WHERE user_id = :user_id");
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':surname', $surname);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

                if ($stmt->execute()) {
                    $success_message = "Profilo aggiornato con successo.";
                    // Aggiorna i dati visualizzati
                    $user['name'] = $name;
                    $user['surname'] = $surname;
                    $user['email'] = $email;
                } else {
                    $error_message = "Errore durante l'aggiornamento del profilo.";
                }
            }
        }

        if (isset($_POST['change_password'])) {
            // Recupera i dati dal form per la password
            $current_password = $_POST['current_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            // Controlla che i campi non siano vuoti
            if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
                $error_message = "Tutti i campi per la modifica della password sono obbligatori.";
            } elseif ($new_password !== $confirm_password) {
                $error_message = "Le nuove password non coincidono.";
            } else {
                // Recupera la password attuale dal database
                $stmt = $conn->prepare("SELECT password FROM gsv_users WHERE user_id = :user_id");
                $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $db_password = $stmt->fetchColumn();

                if (!password_verify($current_password, $db_password)) {
                    $error_message = "La password attuale non è corretta.";
                } else {
                    // Aggiorna la nuova password nel database
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                    $stmt = $conn->prepare("UPDATE gsv_users SET password = :password WHERE user_id = :user_id");
                    $stmt->bindParam(':password', $hashed_password);
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

                    if ($stmt->execute()) {
                        $success_message = "Password aggiornata con successo.";
                    } else {
                        $error_message = "Errore durante l'aggiornamento della password.";
                    }
                }
            }
        }
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
    <title>Modifica Profilo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Modifica Profilo</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="profile.php">Torna al Profilo</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Modifica il tuo Profilo</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger"> <?php echo $error_message; ?> </div>
                        <?php endif; ?>
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success"> <?php echo $success_message; ?> </div>
                        <?php endif; ?>

                        <!-- Form per aggiornare i dati del profilo -->
                        <form method="POST" action="">
                            <div class="mb-3">
                                <label for="name" class="form-label">Nome</label>
                                <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="surname" class="form-label">Cognome</label>
                                <input type="text" name="surname" id="surname" class="form-control" value="<?php echo htmlspecialchars($user['surname']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="update_profile" class="btn btn-primary">Salva Modifiche</button>
                            </div>
                        </form>

                        <hr>

                        <!-- Form per modificare la password -->
                        <form method="POST" action="" class="mt-4">
                            <h5 class="text-center">Modifica Password</h5>
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Password Attuale</label>
                                <input type="password" name="current_password" id="current_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">Nuova Password</label>
                                <input type="password" name="new_password" id="new_password" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Conferma Nuova Password</label>
                                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" name="change_password" class="btn btn-warning">Aggiorna Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
