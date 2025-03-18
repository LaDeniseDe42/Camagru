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
        majInfo();
        header("Location: profile.php?message=" . $result['message'] . "&status=" . $result['status']);
        exit();
    } elseif (isset($_POST['update_username'])) {
        $username = $_POST['username'];
        $result = $changeInfo->updateUsername($username);
        majInfo();
        header("Location: profile.php?message=" . $result['message'] . "&status=" . $result['status']);
        exit();
    } elseif (isset($_POST['update_email'])) {
        $email = $_POST['email'];
        $result = $changeInfo->updateEmail($email);
        majInfo();
        header("Location: profile.php?message=" . $result['message'] . "&status=" . $result['status']);
        exit();
    } elseif (isset($_POST['update_password'])) {
        $password = $_POST['password'];
        $password2 = $_POST['confirm_password'];
        $result = $changeInfo->updatePassword($password, $password2);
        majInfo();
        header("Location: profile.php?message=" . $result['message'] . "&status=" . $result['status']);
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
    <footer>
    </footer>
    <script src="assets/js/modal.js"></script>
</body>
</html>
