<?php
require_once __DIR__ . "/../controllers/AuthController.php";

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
    
    // Appelle la méthode register du contrôleur
    $message = $authController->register($username, $email, $password, $password2);
}

?>


<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
    <link rel="stylesheet" href="/../assets/css/logister.css">
</head>
<body>
    <?php include __DIR__ . "/../Views/auth/register.php"; ?>
    <?php if (!empty($message)) : ?>
    <p class="error-message"><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>
<footer>
</footer>
</body>
</html>
