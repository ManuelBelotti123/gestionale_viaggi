<?php
include('../db/db.php');
include('auth_functions.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $conn->prepare("SELECT user_id, password, user_type FROM gsv_users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_type'] = $user['user_type'];

            if ($user['user_type'] == 'base') {
                header("Location: ../dashboard_user.php");
            } else {
                header("Location: ../dashboard_company.php");
            }
            exit();
        } else {
            $error = "Credenziali errate!";
        }
    } catch (PDOException $e) {
        $error = "Errore nel login: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center">Login</h1>
    <form method="POST" class="mt-4">
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Accedi</button>
    </form>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger mt-3"><?= $error ?></div>
    <?php endif; ?>
</div>
</body>
</html>
