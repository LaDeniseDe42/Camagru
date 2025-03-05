<?php
// Démarre la session pour vérifier si l'utilisateur est connecté
session_start();

// Vérifie si un utilisateur est connecté
$is_logged_in = isset($_SESSION['user']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page d'accueil</title>
    <link rel="stylesheet" href="/../assets/css/styles.css">
</head>
<body>
    <header>
        <h1>Bienvenue sur Camagru</h1>
    </header>

    <main>
        <?php if ($is_logged_in): ?>
            <p>Bonjour, <?php echo htmlspecialchars($_SESSION['user']); ?> !</p>
            <p><a href="logout.php">Se déconnecter</a></p> <!-- Lien pour se déconnecter -->
        <?php else: ?>
            <p>Vous n'êtes pas connecté.</p>
            <p><a href="login.php">Se connecter</a> ou <a href="register.php">S'inscrire</a></p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2025 Camagru. Tous droits réservés.</p>
    </footer>
</body>
</html>
