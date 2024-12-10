<?php
include('../db/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $user_type = $_POST['user_type'];

    try {
        $stmt = $conn->prepare("
            INSERT INTO gsv_users (username, name, surname, email, password, user_type) 
            VALUES (:username, :name, :surname, :email, :password, :user_type)
        ");
        $stmt->execute([
            ':username' => $username,
            ':name' => $name,
            ':surname' => $surname,
            ':email' => $email,
            ':password' => $password,
            ':user_type' => $user_type
        ]);
        header('Location: login.php?success=1');
        exit;
    } catch (PDOException $e) {
        $error_message = "Errore durante la registrazione: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #6c757d, #343a40);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }
        .register-container {
            background: #fff;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 500px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .text-muted a {
            text-decoration: none;
            color: #007bff;
        }
        .text-muted a:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h1 class="text-center mb-4 text-dark">Registrazione</h1>
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" name="username" id="username" class="form-control" placeholder="Inserisci il tuo username" required>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Nome</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Inserisci il tuo nome" required>
            </div>
            <div class="mb-3">
                <label for="surname" class="form-label">Cognome</label>
                <input type="text" name="surname" id="surname" class="form-control" placeholder="Inserisci il tuo cognome" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Inserisci la tua email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Crea una password" required>
            </div>
            <div class="mb-3">
                <label for="user_type" class="form-label">Tipo di Account</label>
                <select name="user_type" id="user_type" class="form-select">
                    <option value="base">Base</option>
                    <option value="ente">Ente</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary w-100">Registrati</button>
        </form>
        <p class="text-muted text-center mt-3">
            Hai già un account? <a href="login.php">Accedi</a>
        </p>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger mt-3"><?= $error ?></div>
        <?php endif; ?>
    </div>
</body>
</html>

