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
require_once __DIR__ . "/../models/User.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];
    
    $result = $authController->resend_pass($email);
    if ($result['status'] === 'success') {
        $successMessage = "Un email a été envoyé. Veuillez vérifier votre boîte de réception pour reinitialiser le mdp.";
        header("Location: login.php?message=" . urlencode($successMessage));
        exit();
    } else {
        $errorMessage = $result['message'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>MDP oublie</title>
    <link rel="stylesheet" href="/../assets/css/logister.css">
    <link rel="stylesheet" href="/../assets/css/navbar.css">
</head>
<body>
    <?php include __DIR__ . '/../Views/auth/navbar.php'; ?>
    <?php include __DIR__ . "/../Views/auth/forgot_password.php"; ?>
    <footer>
    </footer>
</body>
</html>
