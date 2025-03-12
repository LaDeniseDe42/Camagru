<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../controllers/AuthController.php";
require_once __DIR__ . "/../controllers/ChangeInfo.php";
$authController = new AuthController();
$message = "";

requireLogin();
$house = $_SESSION['house'] ?? "Moldu";

$user_mail = $_SESSION['user'] ?? null;
$user_id = $_SESSION['user_id'] ?? null;
$username = $_SESSION['username'] ?? null;
$house = $_SESSION['house'] ?? "Moldu";
$sub_house = whatSubHouse($house);

function majInfo()
{
    $user_mail = $_SESSION['user'] ?? null;
    $user_id = $_SESSION['user_id'] ?? null;
    $username = $_SESSION['username'] ?? null;
    $house = $_SESSION['house'] ?? "Moldu";
    $sub_house = whatSubHouse($house);
}

function whatSubHouse($house)
{
    if ($house === "Gryffondor") {
        return "gryffondor";
    } elseif ($house === "Poufsouffle") {
        return "poufsouffle";
    } elseif ($house === "Serdaigle") {
        return "serdaigle";
    } elseif ($house === "Serpentard") {
        return "serpentard";
    } elseif ($house === "Crakmol") {
        return "crakmol";
    } else {
        return "moldu";
    }
}

$changeInfo = new ChangeInfo();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_house'])) {
        $house = $_POST['house'];
        $result = $changeInfo->updateHouse($house);
        //REGARDER SI LE status du MESSAGE CONTENU DANS RESULT EST UN SUCCES OU UNE ERREUR
        // $result['status'] === 'success' ? $successMessage = $result['message'] : $errorMessage = $result['message'];
        majInfo();
        header("Location: profile.php?message=" . $result['message']);
        exit();
    } elseif (isset($_POST['update_username'])) {
        $username = $_POST['username'];
        $result = $changeInfo->updateUsername($username);
        majInfo();
        header("Location: profile.php?message=" . $result['message']);
        exit();
    } elseif (isset($_POST['update_email'])) {
        $email = $_POST['email'];
        $result = $changeInfo->updateEmail($email);
        majInfo();
        header("Location: profile.php?message=" . $result['message']);
        exit();
    }

}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Profil</title>
    <link rel="stylesheet" href="/../assets/css/profile.css">
    <link rel="stylesheet" href="/../assets/css/navbar.css">
</head>
<body class="<?php echo $house ?>">
    <?php include __DIR__ . '/../Views/auth/navbar.php'; ?>
    <?php include __DIR__ . "/../Views/auth/profile.php"; ?>
<?php if (isset($_GET['message'])) : ?>
        <?php
        $message = $_GET['message'];
        $status = $_GET['status'] ?? 'error';
        ?>
        <div class="error-container">
            <p class="<?= $status === 'success' ? 'success-message' : 'error-message' ?>" style="font-weight: bold; font-size: 1.2em;">
            <?= htmlspecialchars($message); ?>
            </p>
        </div>
    <?php endif; ?>
    <footer>
    </footer>
    <script src="assets/js/modal.js"></script>
</body>
</html>
