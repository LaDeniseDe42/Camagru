<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../controllers/AuthController.php";
require_once __DIR__ . "/../controllers/PhotoController.php";
$message = "";


if (!isLoggedIn())
{
    header("Location: login.php");
    exit();
}

$dbConnection = new Database();
$con = $dbConnection->getConnection();
$UserController = new AuthController();
$my_profile = true;
if (isset($_GET['user']) && $_GET['user'] != $_SESSION['user_id']) {
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

$photoController = new PhotoController($con);
$true_photo = $photoController->getAllImgOfgalleryUserId($this_user_id);


if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file'])){
    $result = $photoController->uploadPhoto($user_id, $_FILES['file']);
    if ($result === false) {
        header("Location: gallery.php?message=" . urlencode("Le format de fichier n est pas valide, les formats acceptés sont jpg, jpeg, png, gif"));
        exit();
    }
    header("Location: gallery.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['photo_id'])) {
    if ($photoController->deleteUserPhoto($_POST['photo_id'], $this_user_id)) {
        $message = "Photo supprimée avec succès !";
    }if (file_exists($photoController->deleteThisImg($this_user_id, $_POST['photo_id'] ))) {
        header("Location: gallery.php");
        exit();
    } else {
        header("Location: gallery.php?message=" . $result['message'] . "&status=" . $result['status']);
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
<body class="<?= htmlspecialchars($this_house) ?>">
    <?php include __DIR__ . '/../Views/auth/navbar.php'; ?>
    <?php include __DIR__ . '/../Views/auth/gallery.php'; ?>

    <footer></footer>
    <script src="assets/js/modal.js"></script>
</body>
</html>
