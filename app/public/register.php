<?php
session_start();
require_once __DIR__ . "/../controllers/AuthController.php";

if (isset($_SESSION['user'])) {
    header("Location: index.php");
}

$authController = new AuthController();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password2 = $_POST['confirm_password'];
    $house = isset($_POST['house']) && $_POST['house'] !== "" ? $_POST['house'] : "Crakmol";



    $result = $authController->register($username, $email, $password, $password2, $house);
    if ($result['status'] === 'success') {
        $successMessage = "Un email de confirmation a été envoyé. Veuillez vérifier votre boîte de réception pour confirmer votre compte.";
        header("Location: login.php?message=" . urlencode($successMessage));
        exit();
    } else {
        $errorMessage = $result['message'];
    }
}

?>


<!DOCTYPE html>
<html>

<head>
    <title>Inscription</title>
    <link rel="stylesheet" href="/../assets/css/logister.css">
    <link rel="stylesheet" href="/../assets/css/navbar.css">
</head>

<body>
    <?php include __DIR__ . '/../Views/auth/navbar.php'; ?>
    <?php include __DIR__ . "/../Views/auth/register.php"; ?>
    <footer>
        <p>&copy; 2025 Camagru. Tous droits réservés par MOI.</p>
    </footer>
    <script src="/../assets/js/navScript.js"></script>
</body>

</html>