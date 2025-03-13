<?php
require_once __DIR__ . "/config/database.php";
require_once __DIR__ . "/config/session.php";

$user_id = $_SESSION['user_id'] ?? null;

if ($user_id) {
    $stmt = $pdo->prepare("SELECT filename, filepath, uploaded_at FROM photos WHERE user_id = ? ORDER BY uploaded_at DESC");
    $stmt->execute([$user_id]);
    $photos = $stmt->fetchAll();
}
?>

<h2>Vos Photos</h2>
<div class="gallery">
    <?php if (!empty($photos)) : ?>
        <?php foreach ($photos as $photo) : ?>
            <div class="photo-card">
                <img src="<?= htmlspecialchars($photo['filepath']); ?>" alt="Photo de l'utilisateur">
                <p>Ajoutée le : <?= $photo['uploaded_at']; ?></p>
            </div>
        <?php endforeach; ?>
    <?php else : ?>
        <p>Aucune photo trouvée.</p>
    <?php endif; ?>
</div>