<?php
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../controllers/AuthController.php";

session_start();

if (isset($_SESSION['user'])) {
    header("Location: index.php");
    exit();
}
$authController = new AuthController();
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'];

    $result = $authController->resend_pass($email);
    if ($result['status'] === 'success') {
        $successMessage = "Un email a été envoyé. Veuillez vérifier votre boîte de réception pour reinitialiser le mdp.";
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
    <title>MDP oublie</title>
    <link rel="stylesheet" href="/../assets/css/logister.css">
    <link rel="stylesheet" href="/../assets/css/navbar.css">
</head>

<body>
    <?php include __DIR__ . '/../Views/auth/navbar.php'; ?>
    <?php include __DIR__ . "/../Views/auth/forgot_password.php"; ?>
    <footer>
        <p>&copy; 2025 Camagru. Tous droits réservés par MOI.</p>
    </footer>
    <script src="/../assets/js/navScript.js"></script>
</body>

</html>