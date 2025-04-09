<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../controllers/AuthController.php";
require_once __DIR__ . "/../controllers/PublicationController.php";
$message = "";

if (!isLoggedIn())
{
    header("Location: login.php");
    exit();
}

// Pour savoir si on est sur son propre profil ou celui d'un autre utilisateur
$dbConnection = new Database();
$con = $dbConnection->getConnection();
$UserController = new AuthController();
$my_profile = true;
if (isset($_GET['user']) && !empty($_GET['user']) && $_GET['user'] != $_SESSION['user_id']) {
    $my_profile = false;
    $this_user_id = $_GET['user'];
    $this_user = $UserController->giveinfoConcernThisUser($this_user_id);
    if ($this_user === false) {
        header("Location: gallery.php?message=" . urlencode("Cet utilisateur n'existe pas"));
        exit();
    }
    $this_username = $this_user['username'];
    $this_email = $this_user['email'];
    $this_house = $this_user['house'];
    $this_sub_house = strtolower($this_house);

} else {
    $this_user_id = $_SESSION['user_id'];
    $this_username = $_SESSION['username'];
    $this_house = $_SESSION['house'];
    $this_sub_house = strtolower($this_house);
    $this_email = $_SESSION['email'];
    $this_user = $_SESSION['user'];
}
// fin des informations concernant la gallerie de l'utilisateur

// pour la publication
$publicationController = new PublicationController($con);
$publications = $publicationController->getPublications($this_user_id);
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['normalFile'])) {
    $type = 'photo';
    $result = $publicationController->uploadPublication($this_user_id, $_FILES['normalFile'], $type);
    if ($result['success']) {
        $message = "Publication envoyée avec succès.";
    } else {
        $message = "Erreur lors de l'envoi de la publication : " . $result['message'];
    }
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])) {
    $type = $_POST['type'] ?? 'photo';
    $result = $publicationController->uploadPublication($this_user_id, $_FILES['file'], $type);
    if ($result['success']) {
        $message = "Publication envoyée avec succès.";
    } else {
        $message = "Erreur lors de l'envoi de la publication : " . $result['message'];
    }
}

// Gestion des likes et dislikes
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['publication_id'])) {
    $publication_id = $_POST['publication_id'];

    // Récupérer la réaction actuelle de l'utilisateur
    $currentReaction = $publicationController->getUserReaction($publication_id, $_SESSION['user_id']);

    if (isset($_POST['like'])) {
        $publicationController->reactToPublication($_SESSION['user_id'], $publication_id, 'like');
    } elseif (isset($_POST['dislike'])) {
        $publicationController->reactToPublication($_SESSION['user_id'], $publication_id, 'dislike');
    }
    // Récupérer l'utilisateur propriétaire de la publication
    $user_id = $publicationController->getUserIdByPublicationId($publication_id);
    header("Location: gallery.php?user=$user_id");
    exit();
}
// fin de pour les likes et dislikes

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['deletePublication'])) {
    $publication_id = $_POST['deletePublication'];
    $publicationController->deletePublication($publication_id, $_SESSION['user_id']);
    $message = "Publication supprimée avec succès.";
    // Rediriger vers la galerie de l'utilisateur
   
    header("Location: gallery.php?user=$_SESSION[user_id]");
    // header("Location: gallery.php?message=" . urlencode("Publication supprimée avec succès"));
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
<body class="<?= htmlspecialchars($this_house) ?>">
    <?php include __DIR__ . '/../Views/auth/navbar.php'; ?>
    <?php include __DIR__ . '/../Views/auth/gallery.php'; ?>

    <footer></footer>
    <script src="assets/js/modal.js"></script>
    <script src="assets/js/cam.js"></script>
</body>
</html>
