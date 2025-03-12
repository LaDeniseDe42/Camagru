<?php
session_start();
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
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password2 = $_POST['confirm_password'];
    $house = $_POST['house'];
    

    $result = $authController->register($username, $email, $password, $password2, $house);
    if ($result['status'] === 'success') {
        $successMessage = "Un email de confirmation a été envoyé. Veuillez vérifier votre boîte de réception pour confirmer votre compte.";
    // Rediriger vers login.php avec le message passé en paramètre dans l'URL
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
    <title>Inscription</title>
    <link rel="stylesheet" href="/../assets/css/logister.css">
    <link rel="stylesheet" href="/../assets/css/navbar.css">
</head>
<body>
    <?php include __DIR__ . '/../Views/auth/navbar.php'; ?>
    <?php include __DIR__ . "/../Views/auth/register.php"; ?>
<footer>
</footer>
</body>
</html>
