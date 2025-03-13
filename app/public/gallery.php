<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../controllers/AuthController.php";

$authController = new AuthController();
requireLogin();

$user_id = $_SESSION['user_id'] ?? null;
$house = $_SESSION['house'] ?? "Moldu";
$message = "";

// Récupération des photos de l'utilisateur connecté
$photos = [];
if ($user_id) {
    $stmt = $pdo->prepare("SELECT filename, filepath, uploaded_at FROM photos WHERE user_id = ? ORDER BY uploaded_at DESC");
    $stmt->execute([$user_id]);
    $photos = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Galerie</title>
    <link rel="stylesheet" href="/../assets/css/profile.css">
    <link rel="stylesheet" href="/../assets/css/navbar.css">
</head>
<body class="<?= htmlspecialchars($house) ?>">
    <?php include __DIR__ . '/../Views/auth/navbar.php'; ?>

    <!-- Affichage des messages de succès ou d'erreur -->
    <?php if (isset($_GET['message'])) : ?>
        <div class="error-container">
            <p class="<?= ($_GET['status'] ?? 'error') === 'success' ? 'success-message' : 'error-message' ?>">
                <?= htmlspecialchars($_GET['message']); ?>
            </p>
        </div>
    <?php endif; ?>

    <h2>Vos Photos</h2>
    <div class="gallery">
        <?php if (!empty($photos)) : ?>
            <?php foreach ($photos as $photo) : ?>
                <div class="photo-card">
                    <img src="<?= htmlspecialchars($photo['filepath']); ?>" alt="Photo de l'utilisateur">
                    <p>Ajoutée le : <?= htmlspecialchars($photo['uploaded_at']); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <p>Aucune photo trouvée.</p>
        <?php endif; ?>
    </div>

    <footer></footer>
    <script src="assets/js/modal.js"></script>
</body>
</html>
