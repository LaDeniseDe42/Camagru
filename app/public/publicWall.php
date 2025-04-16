<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['action']) && $input['action'] === 'load_more') {
        require_once __DIR__ . '/../controllers/loadMorePublications.php';
        exit;
    }
}
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../controllers/AuthController.php";
require_once __DIR__ . "/../controllers/PublicationController.php";

requireLogin();
//recupere les infos de session
$user = $_SESSION['user'];
$email = $_SESSION['email'];
$username =$_SESSION['username'];
$user_id = $_SESSION['user_id'];
$house = $_SESSION['house'];
$sub_house = strtolower($house);

$authController = new AuthController();
$con = $authController->getConnection();
$publicationController = new PublicationController($con);
$allPublications = $publicationController->getNewPublications();


?>

<!DOCTYPE html>
<html>
<head>
    <title>Mur Public</title>
    <link rel="stylesheet" href="/../assets/css/profile.css">
    <link rel="stylesheet" href="/../assets/css/navbar.css">
    <link rel="stylesheet" href="/../assets/css/profile.css">
</head>
<body class="<?= htmlspecialchars($house) ?>">
    <?php include __DIR__ . '/../Views/auth/navbar.php'; ?>
    <?php include __DIR__ . "/../Views/auth/publicWall.php"; ?>


    <footer></footer>
    <script src="assets/js/modal.js"></script>
    <script src="assets/js/wall.js"></script>
</body>
</html>