<?php
// Démarre la session pour vérifier si l'utilisateur est connecté
session_start();

// Vérifie si l'tilisateur est connecté
$is_logged_in = isset($_SESSION['user']);
$user_mail = $_SESSION['user_mail'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? null;

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'accueil</title>
    <link rel="stylesheet" href="/../assets/css/styles.css">
    <link rel="stylesheet" href="/../assets/css/navbar.css">
</head>

<body>
    <?php include(__DIR__ . '/../Views/auth/navbar.php'); ?>
    <div class="background-slider"></div>
    <header>
        <h1>Bienvenue sur Camagru</h1>
    </header>



    <main>
        <?php if ($is_logged_in): ?>
            <p>Bonjour, <?php echo htmlspecialchars($username); ?> !</p>
            <p><a href="logout.php">Se déconnecter</a></p>
        <?php else: ?>
            <p>Vous n'êtes pas connecté.</p>
            <p><a href="login.php">Se connecter</a> ou <a href="register.php">S'inscrire</a></p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 Camagru. Tous droits réservés par MOI.</p>
    </footer>
    <script src="/../assets/js/slider.js"></script>
    <script src="/../assets/js/navScript.js"></script>
</body>

</html>