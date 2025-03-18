<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../controllers/AuthController.php";
require_once __DIR__ . "/../controllers/PhotoController.php";


if (!isLoggedIn())
{
    header("Location: login.php");
    exit();
}
//recupere les infos de session
$user = $_SESSION['user'];
$email = $_SESSION['email'];
$username =$_SESSION['username'];
$user_id = $_SESSION['user_id'];
$house = $_SESSION['house'];
$sub_house = strtolower($house);

$message = "";
$dbConnection = new Database();
$con = $dbConnection->getConnection();
$photoController = new PhotoController($con);
$photos = $photoController->getUserPhotos($_SESSION['user_id']);
$true_photo = $photoController->getAllImgOfgalleryUserId($user_id);

// var_dump($_POST);
// var_dump($_FILES);
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])){
    $photoController->uploadPhoto($user_id, $_FILES['file']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['photo_id'])) {
    if ($photoController->deleteUserPhoto($_POST['photo_id'], $user_id)) {
        $message = "Photo supprimée avec succès !";
    }if (file_exists($photoController->deleteThisImg($user_id, $_POST['photo_id']))) {
        $message = "Photo supprimée avec succès !";
    } else {
        $message = "Échec de la suppression.";
    }
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
    <?php include __DIR__ . '/../Views/auth/gallery.php'; ?>

    <footer></footer>
    <script src="assets/js/modal.js"></script>
</body>
</html>
