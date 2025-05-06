<?php
session_start();
require_once __DIR__ . "/../controllers/AuthController.php";

// Vérifie si l'utilisateur est déjà connecté
if (isset($_SESSION['user'])) {
    header("Location: index.php");
}
$authController = new AuthController();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emailOrUsername = $_POST['email'];
    $password = $_POST['password'];

    $result = $authController->login($emailOrUsername, $password);
    if ($result['status'] === 'success') {
        $_SESSION['user'] = $emailOrUsername; // Stocke l'email ou l'username en session
        $_SESSION['email'] = $result['email'];
        $_SESSION['username'] = $result['username'];
        $_SESSION['user_id'] = $result['user_id'];
        $_SESSION['house'] = $result['house'];
        $csrf_token = bin2hex(random_bytes(32)); // Génère un token CSRF
        $_SESSION['csrf_token'] = $csrf_token; // Stocke le token CSRF en session
        header("Location: index.php");
        exit();
    } else {
        $errorMessage = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Connexion</title>
    <link rel="stylesheet" href="/../assets/css/logister.css">
    <link rel="stylesheet" href="/../assets/css/navbar.css">
</head>

<body>
    <?php include __DIR__ . '/../Views/auth/navbar.php'; ?>
    <?php include __DIR__ . "/../Views/auth/login.php"; ?>
    <footer>
        <p>&copy; 2025 Camagru. Tous droits réservés par MOI.</p>
    </footer>
    <script src="/../assets/js/navScript.js"></script>
</body>

</html>