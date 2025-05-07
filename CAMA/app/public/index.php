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

<?php if ($is_logged_in): ?>

    <body style="background-image: url('/../assets/img/PixelHarry2.png'); background-repeat: repeat; background-position: center; background-size: cover;">
    <?php else: ?>

        <body style="background-image: url('/../assets/img/PixelHarry.png'); background-repeat: repeat; background-position: center; background-size: cover;">
        <?php endif; ?>
        <?php include(__DIR__ . '/../Views/auth/navbar.php'); ?>
        <header style="background-image: url('/../assets/img/acceuil.png'); background-repeat: repeat; background-size: auto 100%; background-position: center; opacity: 0.85; height: 100px; border: 0.5px solid black;">
            <h1>Bienvenue sur Camagru</h1>
        </header>



        <main>
            <?php if ($is_logged_in): ?>
                <div style="background-color: rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 10px;">
                    <p style="color: white;"><strong>Bonjour, <?php echo htmlspecialchars($username); ?> !</strong></p>
                    <p><a href="logout.php">Se déconnecter</a></p>
                </div>
            <?php else: ?>
                <div style="background-color: rgba(0, 0, 0, 0.5); padding: 20px; border-radius: 10px;">
                    <p style="color: white;"><strong>Vous n'êtes pas connecté.</strong></p>
                    <p style="color: white;"><a href="login.php">Se connecter</a><strong> ou </strong><a href="register.php">S'inscrire</a></p>
                </div>
            <?php endif; ?>
        </main>

        <footer>
            <p>&copy; 2025 Camagru. Tous droits réservés par MOI.</p>
        </footer>
        <script src="/../assets/js/navScript.js"></script>
        </body>

</html>