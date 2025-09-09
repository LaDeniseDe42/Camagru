<?php
require_once __DIR__ . "/../config/setup.php";
require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../controllers/AuthController.php";
require_once __DIR__ . "/../controllers/PublicationController.php";
$message = "";

requireLogin();

// Pour savoir si on est sur son propre profil ou celui d'un autre utilisateur
$UserController = new AuthController();
$con = $UserController->getConnection();
$my_profile = true;
if (isset($_GET['user']) && !empty($_GET['user']) && $_GET['user'] != $_SESSION['user_id']) {
    $my_profile = false;
    $this_user_id = $_GET['user'];
    $this_user = $UserController->giveinfoConcernThisUser($this_user_id);
    if ($this_user === false) {
        header("Location: 404.php");
        exit();
    }
    $this_username = $this_user['username'];
    $this_email = $this_user['email'];
    $this_house = $this_user['house'];
    $this_sub_house = strtolower($this_house);
    $bestScore = $UserController->getBestScoreOfThisUser($this_user_id);
    if ($bestScore === null) {
        $bestScore = 0;
    }
} else {
    $this_user_id = $_SESSION['user_id'];
    $this_username = $_SESSION['username'];
    $this_house = $_SESSION['house'];
    $this_sub_house = strtolower($this_house);
    $this_email = $_SESSION['email'];
    $this_user = $_SESSION['user'];
    $bestScore = $UserController->getBestScore($_SESSION['user_id']);
    if ($bestScore === null) {
        $bestScore = 0;
    }
}


// pour la publication
$publicationController = new PublicationController($con);
$publications = $publicationController->getPublications($this_user_id);
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['normalFile'])) {
    $type = 'photo';

    $result = $publicationController->uploadPublication($this_user_id, $_FILES['normalFile'], $type);
    if ($result['success'] === true) {
        $message = "Publication envoyée avec succès.";
        header("Location: gallery.php?user=$this_user_id");
        exit();
    } else {
        $message = "Erreur lors de l'envoi de la publication : " . $result['message'];
        header("Location: gallery.php?user=$this_user_id&message=" . urlencode($message));
        exit();
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $type = $_POST['type'] ?? 'photo';
    $result = $publicationController->uploadPublication($this_user_id, $_FILES['file'], $type);
    if ($result['success'] === true) {
        $message = "Publication envoyée avec succès.";
    } else {
        $message = "Erreur lors de l'envoi de la publication : " . $result['message'];
    }
}

// Pour les likes et dislikes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['publication_id']) && isset($_POST['csrf_token'])) {
    if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode(['status' => 'error', 'message' => 'Token CSRF invalide.']);
        exit;
    }

    $userId = $_SESSION['user_id'] ?? null;
    $publicationId = $_POST['publication_id'];
    if (!$userId) {
        echo json_encode(['status' => 'error', 'message' => 'Utilisateur non connecté.']);
        exit;
    }
    $reaction = null;
    if (isset($_POST['like'])) {
        $reaction = 'like';
    } elseif (isset($_POST['dislike'])) {
        $reaction = 'dislike';
    }
    if ($reaction) {
        $result = $publicationController->reactToPublication($userId, $publicationId, $reaction);
        if ($result && isset($result['nb_likes'], $result['nb_dislikes'])) {
            echo json_encode([
                'status' => 'success',
                'nb_likes' => $result['nb_likes'],
                'nb_dislikes' => $result['nb_dislikes'],
                'user_reaction' => $result['user_reaction'],
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Échec de la réaction.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Aucune action valide.']);
    }
    exit;
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deletePublication'])) {
    $publication_id = $_POST['deletePublication'];
    $publicationController->deletePublication($publication_id, $_SESSION['user_id']);
    $message = "Publication supprimée avec succès.";
    header("Location: gallery.php?user=$_SESSION[user_id]");
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Galerie</title>
    <link rel="stylesheet" href="/../assets/css/profile.css">
    <link rel="stylesheet" href="/../assets/css/navbar.css">
    <link rel="stylesheet" href="/../assets/css/galleryStyle.css">
</head>

<body class="Theme-<?= htmlspecialchars($this_house) ?>">
    <?php include __DIR__ . '/../Views/auth/navbar.php'; ?>
    <?php include __DIR__ . '/../Views/auth/gallery.php'; ?>
    <script src="/assets/js/AllCamStuff.js"></script>
    <script src="/assets/js/update_gallery.js"></script>
    <?php if ($my_profile): ?>
        <script src="/assets/js/uploadBasic.js"></script>
    <?php endif; ?>
    <script src="/../assets/js/navScript.js"></script>

    <footer>
        <p>&copy; 2025 Camagru. Tous droits réservés par MOI.</p>
    </footer>
</body>

</html>