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

if (isset($_GET["token"]) && $_SERVER["REQUEST_METHOD"] === "POST") {
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];
    if ($password !== $confirm_password) {
        echo "Les mots de passe ne correspondent pas.";
        exit();
    } else {
        $result = $authController->reset_password($password, $_GET["token"]);
        if ($result['status'] === 'success') {
            $successMessage = "Votre mot de passe a été réinitialisé avec succès.";
            header("Location: login.php?message=" . urlencode($successMessage));
            exit();
        } else {
            $errorMessage = $result['message'];
        }
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
    <div class="container">
        <form method="post">
            <input type="password" name="password" placeholder="Nouveau mot de passe" required>
            <input type="password" name="confirm_password" placeholder="Confirmez le mot de passe" required>
            <button type="submit">Réinitialiser</button>
        </form>
        <!-- Message de succès -->
        <?php if (!empty($successMessage)): ?>
            <div class="error-container">
                <p class="success-message"><?= htmlspecialchars($successMessage); ?></p>
            </div>
        <?php endif; ?>

        <!-- Message d'erreur -->
        <?php if (!empty($errorMessage)): ?>
            <div class="error-container">
                <p class="error-message"><?= htmlspecialchars($errorMessage); ?></p>
            </div>
        <?php endif; ?>
    </div>
    <br>
    </div>
    <footer>
        <p>&copy; 2025 Camagru. Tous droits réservés par MOI.</p>
    </footer>
    <script src="/../assets/js/navScript.js"></script>
</body>

</html>