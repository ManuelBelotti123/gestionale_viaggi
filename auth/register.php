<?php
include('../db/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $user_type = $_POST['user_type']; // "base" o "ente"

    try {
        $stmt = $conn->prepare("INSERT INTO gsv_users (username, name, surname, email, password, user_type, created_at) 
                                VALUES (:username, :name, :surname, :email, :password, :user_type, NOW())");

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':surname', $surname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':user_type', $user_type);

        $stmt->execute();

        header("Location: login.php?success=1");
        exit();
    } catch (PDOException $e) {
        $error = "Errore nella registrazione: " . $e->getMessage();
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
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Registrazione</h1>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Nome</label>
            <input type="text" name="name" id="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="surname" class="form-label">Cognome</label>
            <input type="text" name="surname" id="surname" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="user_type" class="form-label">Tipo di Account</label>
            <select name="user_type" id="user_type" class="form-select">
                <option value="base">Base</option>
                <option value="ente">Azienda</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Registrati</button>
    </form>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger mt-3"><?= $error ?></div>
    <?php endif; ?>
</div>
</body>
</html>
