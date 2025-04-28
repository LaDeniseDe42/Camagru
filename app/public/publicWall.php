<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['action']) && $input['action'] === 'load_more') {
        require_once __DIR__ . '/../controllers/loadMorePublications.php';
        exit;
    }
}
require_once __DIR__ . "/../config/setup.php";
require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../controllers/AuthController.php";
require_once __DIR__ . "/../controllers/PublicationController.php";

$isLog = isLoggedIn();

if ($isLog) {
    $user = $_SESSION['user'];
    $email = $_SESSION['email'];
    $username = $_SESSION['username'];
    $user_id = $_SESSION['user_id'];
    $house = $_SESSION['house'];
    $sub_house = strtolower($house);
} else {
    $user = null;
    $email = null;
    $username = "AnonymousUser";
    $user_id = null;
    $house = "Crakmol";
    $sub_house = "crakmol";
}

$authController = new AuthController();
$con = $authController->getConnection();
$publicationController = new PublicationController($con);
$allPublications = $publicationController->getNewPublications();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['action'])) {
        if ($input['action'] === 'react') {
            $publicationId = $input['publication_id'] ?? null;
            $reaction = $input['reaction'] ?? null;

            if (!$user_id || !$publicationId || !in_array($reaction, ['like', 'dislike'])) {
                echo json_encode(['status' => 'error', 'message' => 'Paramètres manquants ou invalides.']);
                exit;
            }

            $result = $publicationController->reactToPublication($user_id, $publicationId, $reaction);

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
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Mur Public</title>
    <link rel="stylesheet" href="/../assets/css/profile.css">
    <link rel="stylesheet" href="/../assets/css/navbar.css">
    <link rel="stylesheet" href="/../assets/css/profile.css">
</head>

<body class="Theme-<?= htmlspecialchars($house) ?>">
    <?php include __DIR__ . '/../Views/auth/navbar.php'; ?>
    <?php include __DIR__ . "/../Views/auth/publicWall.php"; ?>


    <footer>
        <p>&copy; 2025 Camagru. Tous droits réservés par MOI.</p>
    </footer>
    <script src="assets/js/modal.js"></script>
    <script src="assets/js/wall.js"></script>
    <script src="assets/js/navScript.js"></script>
</body>

</html>