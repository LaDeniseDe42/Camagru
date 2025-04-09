<?php
session_start(); // Démarre la session pour pouvoir utiliser $_SESSION
require_once __DIR__ . "/../controllers/AuthController.php";

// Vérifie si l'utilisateur est déjà connecté
if (isset($_SESSION['user'])) {
    header("Location: index.php"); // Redirige vers la page d'accueil s'il est déjà connecté
    exit();
}
// Créer une instance de AuthController
$authController = new AuthController();

// Message d'erreur ou de succès
$message = "";

// Vérifie si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupère les données du formulaire
    $emailOrUsername= $_POST['email'];
    $password = $_POST['password'];
    
    $result = $authController->login($emailOrUsername, $password);
    if ($result['status'] === 'success') {
        $_SESSION['user'] = $emailOrUsername; // Stocke l'email ou l'username en session
        $_SESSION['email'] = $result['email']; // Stocke l'email en session
        $_SESSION['username'] = $result['username']; // Stocke le nom d'utilisateur en session
        $_SESSION['user_id'] = $result['user_id']; // Stocke l'id de l'utilisateur en session
        $_SESSION['house'] = $result['house']; // Stocke la maison de l'utilisateur en session
        $csrf_token = bin2hex(random_bytes(32)); // Génère un token CSRF
        $_SESSION['csrf_token'] = $csrf_token; // Stocke le token CSRF en session
        header("Location: index.php"); // Redirige vers l'accueil
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
    </footer>
</body>
</html>
